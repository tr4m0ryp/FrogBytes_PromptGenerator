/**
 * Enhanced Prompt Sync with Offline Support
 * This is an enhanced version of prompt-sync.js with better error handling and offline support
 * enhanced-prompt-sync.js
 */

class EnhancedPromptSync {
    constructor() {
        this.isLoggedIn = false;
        this.syncInProgress = false;
        this.lastSyncTime = null;
        this.syncQueue = [];
        this.isOnline = navigator.onLine;
        this.syncRetryCount = 0;
        this.maxRetries = 3;
        
        // Listen for online/offline events
        window.addEventListener('online', () => {
            this.isOnline = true;
            this.processSyncQueue();
        });
        
        window.addEventListener('offline', () => {
            this.isOnline = false;
        });
        
        // Load last sync time from localStorage
        this.loadSyncMetadata();
    }

    // Enhanced login status with user information
    setLoginStatus(isLoggedIn, userId = null) {
        this.isLoggedIn = isLoggedIn;
        this.userId = userId;
        
        if (isLoggedIn && this.isOnline) {
            // Trigger sync when user logs in
            setTimeout(() => this.syncPrompts(), 1000);
        }
    }

    // Load sync metadata from localStorage
    loadSyncMetadata() {
        try {
            const metadata = localStorage.getItem('frogbytes-sync-metadata');
            if (metadata) {
                const data = JSON.parse(metadata);
                this.lastSyncTime = data.lastSyncTime ? new Date(data.lastSyncTime) : null;
                this.syncQueue = data.syncQueue || [];
            }
        } catch (e) {
            console.error('Error loading sync metadata:', e);
        }
    }

    // Save sync metadata to localStorage
    saveSyncMetadata() {
        try {
            const metadata = {
                lastSyncTime: this.lastSyncTime ? this.lastSyncTime.toISOString() : null,
                syncQueue: this.syncQueue
            };
            localStorage.setItem('frogbytes-sync-metadata', JSON.stringify(metadata));
        } catch (e) {
            console.error('Error saving sync metadata:', e);
        }
    }

    // Enhanced server communication with retry logic
    async makeRequest(url, options = {}) {
        const defaultOptions = {
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            }
        };

        const finalOptions = { ...defaultOptions, ...options };

        for (let attempt = 0; attempt <= this.maxRetries; attempt++) {
            try {
                const response = await fetch(url, finalOptions);
                
                if (!response.ok) {
                    if (response.status === 401) {
                        // User is no longer authenticated
                        this.setLoginStatus(false);
                        throw new Error('User not authenticated');
                    }
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                this.syncRetryCount = 0; // Reset retry count on success
                return data;
                
            } catch (error) {
                console.error(`Request attempt ${attempt + 1} failed:`, error);
                
                if (attempt === this.maxRetries) {
                    throw error;
                }
                
                // Wait before retrying (exponential backoff)
                await new Promise(resolve => setTimeout(resolve, Math.pow(2, attempt) * 1000));
            }
        }
    }

    // Enhanced server loading with conflict detection
    async loadFromServer() {
        if (!this.isLoggedIn || !this.isOnline) {
            return [];
        }

        try {
            const data = await this.makeRequest('/api/prompts.php', { method: 'GET' });
            
            if (data.success) {
                const serverPrompts = data.prompts.map(prompt => ({
                    id: parseInt(prompt.id),
                    title: prompt.title,
                    content: prompt.content,
                    category: prompt.category || 'custom',
                    serverId: parseInt(prompt.id),
                    lastModified: new Date(prompt.updated_at)
                }));

                console.log('Loaded prompts from server:', serverPrompts.length);
                return serverPrompts;
            } else {
                throw new Error(data.message || 'Failed to load prompts from server');
            }
        } catch (error) {
            console.error('Error loading prompts from server:', error);
            return [];
        }
    }

    // Queue operations for offline sync
    queueOperation(operation) {
        this.syncQueue.push({
            ...operation,
            timestamp: new Date().toISOString(),
            id: Date.now() + Math.random()
        });
        this.saveSyncMetadata();
        
        if (this.isOnline && this.isLoggedIn) {
            this.processSyncQueue();
        }
    }

    // Process queued operations
    async processSyncQueue() {
        if (!this.isLoggedIn || !this.isOnline || this.syncQueue.length === 0) {
            return;
        }

        const queue = [...this.syncQueue];
        this.syncQueue = [];

        for (const operation of queue) {
            try {
                await this.executeOperation(operation);
            } catch (error) {
                console.error('Failed to execute queued operation:', error);
                // Re-queue failed operations
                this.syncQueue.push(operation);
            }
        }

        this.saveSyncMetadata();
    }

    // Execute a single operation
    async executeOperation(operation) {
        switch (operation.type) {
            case 'save':
                return await this.saveToServer(operation.prompt);
            case 'delete':
                return await this.deleteFromServer(operation.prompt);
            default:
                console.warn('Unknown operation type:', operation.type);
        }
    }

    // Enhanced save with offline queueing
    async saveToServer(prompt, queueIfOffline = true) {
        if (!this.isLoggedIn) {
            return { success: false, message: 'Not logged in' };
        }

        if (!this.isOnline && queueIfOffline) {
            this.queueOperation({ type: 'save', prompt });
            return { success: true, message: 'Queued for sync when online', queued: true };
        }

        try {
            const method = prompt.serverId ? 'PUT' : 'POST';
            const payload = {
                title: prompt.title,
                content: prompt.content,
                category: prompt.category || 'custom'
            };

            if (method === 'PUT') {
                payload.id = prompt.serverId;
            }

            const data = await this.makeRequest('/api/prompts.php', {
                method: method,
                body: JSON.stringify(payload)
            });
            
            if (data.success && data.prompt) {
                prompt.serverId = parseInt(data.prompt.id);
                prompt.lastModified = new Date(data.prompt.updated_at);
                console.log('Saved prompt to server:', prompt.title);
            }
            
            return data;
        } catch (error) {
            console.error('Error saving prompt to server:', error);
            
            if (queueIfOffline) {
                this.queueOperation({ type: 'save', prompt });
                return { success: true, message: 'Queued for sync when online', queued: true };
            }
            
            return { success: false, message: error.message };
        }
    }

    // Enhanced delete with offline queueing
    async deleteFromServer(prompt, queueIfOffline = true) {
        if (!this.isLoggedIn || !prompt.serverId) {
            return { success: false, message: 'Not logged in or no server ID' };
        }

        if (!this.isOnline && queueIfOffline) {
            this.queueOperation({ type: 'delete', prompt });
            return { success: true, message: 'Queued for sync when online', queued: true };
        }

        try {
            const data = await this.makeRequest(`/api/prompts.php?id=${prompt.serverId}`, {
                method: 'DELETE'
            });
            
            console.log('Deleted prompt from server:', prompt.title);
            return data;
        } catch (error) {
            console.error('Error deleting prompt from server:', error);
            
            if (queueIfOffline) {
                this.queueOperation({ type: 'delete', prompt });
                return { success: true, message: 'Queued for sync when online', queued: true };
            }
            
            return { success: false, message: error.message };
        }
    }

    // Enhanced sync with conflict resolution
    async syncPrompts(force = false) {
        if (!this.isLoggedIn || !this.isOnline) {
            return { success: false, message: 'Not logged in or offline' };
        }

        if (this.syncInProgress && !force) {
            return { success: false, message: 'Sync already in progress' };
        }

        this.syncInProgress = true;
        console.log('Starting enhanced prompt sync...');

        try {
            // Process any queued operations first
            await this.processSyncQueue();

            // Load current state
            const localPrompts = this.loadLocalPrompts();
            const serverPrompts = await this.loadFromServer();
            
            // Detect conflicts (prompts modified both locally and on server)
            const conflicts = this.detectConflicts(localPrompts, serverPrompts);
            
            if (conflicts.length > 0) {
                console.warn('Sync conflicts detected:', conflicts);
                // For now, server wins (could be enhanced with user choice)
            }

            // Merge prompts with conflict resolution
            const mergedPrompts = this.mergePrompts(localPrompts, serverPrompts);

            // Update local storage
            window.userPrompts = mergedPrompts;
            this.saveLocalPrompts(mergedPrompts);
            
            // Update sync metadata
            this.lastSyncTime = new Date();
            this.saveSyncMetadata();
            
            console.log('Enhanced sync completed. Total prompts:', mergedPrompts.length);
            
            // Refresh UI
            if (typeof renderUserPrompts === 'function') {
                renderUserPrompts();
                populateSystemPromptSelect();
            }

            return { success: true, message: 'Sync completed successfully' };

        } catch (error) {
            console.error('Error during enhanced sync:', error);
            return { success: false, message: error.message };
        } finally {
            this.syncInProgress = false;
        }
    }

    // Detect conflicts between local and server prompts
    detectConflicts(localPrompts, serverPrompts) {
        const conflicts = [];
        
        localPrompts.forEach(local => {
            if (local.serverId) {
                const server = serverPrompts.find(s => s.serverId === local.serverId);
                if (server) {
                    // Check if both have been modified since last sync
                    const localModified = local.lastModified && this.lastSyncTime && 
                        new Date(local.lastModified) > this.lastSyncTime;
                    const serverModified = server.lastModified && this.lastSyncTime && 
                        server.lastModified > this.lastSyncTime;
                    
                    if (localModified && serverModified && 
                        (local.title !== server.title || local.content !== server.content)) {
                        conflicts.push({ local, server });
                    }
                }
            }
        });
        
        return conflicts;
    }

    // Merge local and server prompts with conflict resolution
    mergePrompts(localPrompts, serverPrompts) {
        const merged = [];
        const processedServerIds = new Set();

        // Process server prompts first (they are the source of truth)
        serverPrompts.forEach(serverPrompt => {
            const localMatch = localPrompts.find(local => local.serverId === serverPrompt.serverId);
            
            merged.push({
                id: localMatch ? localMatch.id : this.getNextLocalId(),
                title: serverPrompt.title,
                content: serverPrompt.content,
                category: serverPrompt.category,
                serverId: serverPrompt.serverId,
                lastModified: serverPrompt.lastModified
            });
            
            processedServerIds.add(serverPrompt.serverId);
        });

        // Process local-only prompts (not yet synced)
        localPrompts.forEach(localPrompt => {
            if (!localPrompt.serverId || !processedServerIds.has(localPrompt.serverId)) {
                merged.push({
                    ...localPrompt,
                    id: localPrompt.id || this.getNextLocalId()
                });
            }
        });

        return merged;
    }

    // Get sync status for UI
    getSyncStatus() {
        return {
            isLoggedIn: this.isLoggedIn,
            isOnline: this.isOnline,
            syncInProgress: this.syncInProgress,
            lastSyncTime: this.lastSyncTime,
            queuedOperations: this.syncQueue.length,
            needsSync: this.syncQueue.length > 0 || 
                      (this.lastSyncTime === null && this.isLoggedIn)
        };
    }

    // Utility methods
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

    getNextLocalId() {
        const existing = window.userPrompts || [];
        return existing.length > 0 ? Math.max(...existing.map(p => p.id || 0)) + 1 : 1;
    }
}

// Global instance - uncomment to use enhanced version
// window.promptSync = new EnhancedPromptSync();
