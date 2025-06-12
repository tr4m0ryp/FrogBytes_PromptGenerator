// Server sync for user prompts
// prompt-sync.js

class PromptSync {
    constructor() {
        this.isLoggedIn = false;
        this.syncInProgress = false;
    }

    // Check if user is logged in (we'll pass this from PHP)
    setLoginStatus(isLoggedIn) {
        this.isLoggedIn = isLoggedIn;
    }

    // Load prompts from server
    async loadFromServer() {
        if (!this.isLoggedIn) {
            console.log('User not logged in, skipping server load');
            return [];
        }

        try {
            const response = await fetch('/api/prompts.php', {
                method: 'GET',
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
            if (data.success) {
                console.log('Loaded prompts from server:', data.prompts.length);
                return data.prompts.map(prompt => ({
                    id: parseInt(prompt.id),
                    title: prompt.title,
                    content: prompt.content,
                    category: prompt.category || 'custom',
                    serverId: parseInt(prompt.id) // Keep track of server ID
                }));
            } else {
                console.error('Failed to load prompts from server:', data.message);
                return [];
            }
        } catch (error) {
            console.error('Error loading prompts from server:', error);
            return [];
        }
    }

    // Save prompt to server
    async saveToServer(prompt) {
        if (!this.isLoggedIn) {
            console.log('User not logged in, skipping server save');
            return { success: false, message: 'Not logged in' };
        }

        try {
            const method = prompt.serverId ? 'PUT' : 'POST';
            const url = '/api/prompts.php';
            
            const payload = {
                title: prompt.title,
                content: prompt.content,
                category: prompt.category || 'custom'
            };

            if (method === 'PUT') {
                payload.id = prompt.serverId;
            }

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify(payload)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
            if (data.success && data.prompt) {
                // Update the prompt with server ID
                prompt.serverId = parseInt(data.prompt.id);
                console.log('Saved prompt to server:', prompt.title);
            }
            
            return data;
        } catch (error) {
            console.error('Error saving prompt to server:', error);
            return { success: false, message: error.message };
        }
    }

    // Delete prompt from server
    async deleteFromServer(prompt) {
        if (!this.isLoggedIn || !prompt.serverId) {
            console.log('User not logged in or no server ID, skipping server delete');
            return { success: false, message: 'Not logged in or no server ID' };
        }

        try {
            const response = await fetch(`/api/prompts.php?id=${prompt.serverId}`, {
                method: 'DELETE',
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Deleted prompt from server:', prompt.title);
            return data;
        } catch (error) {
            console.error('Error deleting prompt from server:', error);
            return { success: false, message: error.message };
        }
    }

    // Import local prompts to server (for first-time login)
    async importLocalPrompts(localPrompts) {
        if (!this.isLoggedIn || this.syncInProgress) {
            return { success: false, message: 'Not logged in or sync in progress' };
        }

        this.syncInProgress = true;

        try {
            const response = await fetch('/api/prompts.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    action: 'import',
                    prompts: localPrompts
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Import result:', data);
            return data;
        } catch (error) {
            console.error('Error importing prompts:', error);
            return { success: false, message: error.message };
        } finally {
            this.syncInProgress = false;
        }
    }

    // Sync local prompts with server (merge strategy)
    async syncPrompts() {
        if (!this.isLoggedIn || this.syncInProgress) {
            return;
        }

        this.syncInProgress = true;
        console.log('Starting prompt sync...');

        try {
            // 1. Load existing local prompts
            const localPrompts = this.loadLocalPrompts();
            
            // 2. Load prompts from server
            const serverPrompts = await this.loadFromServer();
            
            // 3. Check if we have any local prompts that aren't on the server
            const localOnlyPrompts = localPrompts.filter(local => 
                !local.serverId && !serverPrompts.some(server => 
                    server.title === local.title && server.content === local.content
                )
            );

            // 4. Import local-only prompts to server
            if (localOnlyPrompts.length > 0) {
                console.log(`Importing ${localOnlyPrompts.length} local prompts to server...`);
                await this.importLocalPrompts(localOnlyPrompts);
                
                // Reload server prompts after import
                const updatedServerPrompts = await this.loadFromServer();
                
                // Update local prompts with server IDs
                this.mergeWithServerIds(localPrompts, updatedServerPrompts);
            } else {
                // Just merge with existing server prompts
                this.mergeWithServerIds(localPrompts, serverPrompts);
            }

            // 5. Use server prompts as the source of truth
            window.userPrompts = serverPrompts.map(serverPrompt => {
                // Find matching local prompt to preserve local ID structure
                const localMatch = localPrompts.find(local => 
                    local.serverId === serverPrompt.id ||
                    (local.title === serverPrompt.title && local.content === serverPrompt.content)
                );

                return {
                    id: localMatch ? localMatch.id : this.getNextLocalId(),
                    title: serverPrompt.title,
                    content: serverPrompt.content,
                    category: serverPrompt.category || 'custom',
                    serverId: serverPrompt.id
                };
            });

            // 6. Save updated prompts to localStorage
            this.saveLocalPrompts(window.userPrompts);
            
            console.log('Prompt sync completed. Total prompts:', window.userPrompts.length);
            
            // 7. Refresh the UI
            if (typeof renderUserPrompts === 'function') {
                renderUserPrompts();
                populateSystemPromptSelect();
            }

        } catch (error) {
            console.error('Error during prompt sync:', error);
        } finally {
            this.syncInProgress = false;
        }
    }

    // Helper methods
    loadLocalPrompts() {
        try {
            const saved = localStorage.getItem('frogbytes-user-prompts');
            return saved ? JSON.parse(saved) : [];
        } catch (e) {
            console.error('Error loading local prompts:', e);
            return [];
        }
    }

    saveLocalPrompts(prompts) {
        try {
            localStorage.setItem('frogbytes-user-prompts', JSON.stringify(prompts));
        } catch (e) {
            console.error('Error saving local prompts:', e);
        }
    }

    mergeWithServerIds(localPrompts, serverPrompts) {
        localPrompts.forEach(local => {
            const serverMatch = serverPrompts.find(server => 
                server.title === local.title && server.content === local.content
            );
            if (serverMatch) {
                local.serverId = serverMatch.id;
            }
        });
        this.saveLocalPrompts(localPrompts);
    }

    getNextLocalId() {
        const existing = window.userPrompts || [];
        return existing.length > 0 ? Math.max(...existing.map(p => p.id || 0)) + 1 : 1;
    }
}

// Global instance
window.promptSync = new PromptSync();
