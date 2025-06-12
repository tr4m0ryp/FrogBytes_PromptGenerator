<?php
// Check login status for index page
// index.php

require_once __DIR__ . '/includes/User.php';

$user = new User();
$isLoggedIn = $user->isLoggedIn();
$currentUser = null;

if ($isLoggedIn) {
    $currentUser = $user->getCurrentUser();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">    
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, maximum-scale=1.0">
    <title>FrogBytes's Prompt Helper</title>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#3f51b5">
    <meta name="mobile-web-app-capable" content="yes">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="ajax/libs/font-awesome/6.0.0/css/all.min.css" crossorigin="anonymous">    
    <style>
      /* Include the existing CSS from index.htm - keeping it the same */
      :root {
        --bg-primary: #0D1117;
        --bg-secondary: #161B22;
        --bg-tertiary: #101A27;
        --border-color: #30363D;
        --text-primary: #C9D1D9;
        --text-secondary: #8B949E;
        --text-tertiary: #E0E0E0;
        --accent-primary: #39FF14;
        --accent-secondary: #32CD32;
        --accent-tertiary: #00FF32;
        --error-color: #FF6B6B;
        --shadow-accent: rgba(57, 255, 20, 0.1);
        --shadow-accent-hover: rgba(57, 255, 20, 0.3);
        --glow-accent: rgba(57, 255, 20, 0.5);
      }

      body.light-theme {
        --bg-primary: #FFFFFF;
        --bg-secondary: #F6F8FA;
        --bg-tertiary: #FAFBFC;
        --border-color: #D0D7DE;
        --text-primary: #24292F;
        --text-secondary: #656D76;
        --text-tertiary: #24292F;
        --accent-primary: #39FF14;
        --accent-secondary: #32CD32;
        --accent-tertiary: #00FF32;
        --error-color: #DA3633;
        --shadow-accent: rgba(57, 255, 20, 0.1);
        --shadow-accent-hover: rgba(57, 255, 20, 0.2);
        --glow-accent: rgba(57, 255, 20, 0.4);
      }

      body {
        font-family: 'Inter', sans-serif;
        background-color: var(--bg-primary);
        background-image: linear-gradient(45deg, rgba(20, 25, 35, 0.5) 25%, transparent 25%, transparent 75%, rgba(20, 25, 35, 0.5) 75%, rgba(20, 25, 35, 0.5)),
                          linear-gradient(45deg, rgba(20, 25, 35, 0.5) 25%, transparent 25%, transparent 75%, rgba(20, 25, 35, 0.5) 75%, rgba(20, 25, 35, 0.5));
        background-size: 6px 6px;
        background-position: 0 0, 3px 3px;
        margin: 0;
        padding: 0;
        color: var(--text-primary);
        line-height: 1.6;
        transition: background-color 0.3s ease, color 0.3s ease;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
      }

      body.light-theme {
        background-image: linear-gradient(45deg, rgba(200, 205, 215, 0.3) 25%, transparent 25%, transparent 75%, rgba(200, 205, 215, 0.3) 75%, rgba(200, 205, 215, 0.3)),
                          linear-gradient(45deg, rgba(200, 205, 215, 0.3) 25%, transparent 25%, transparent 75%, rgba(200, 205, 215, 0.3) 75%, rgba(200, 205, 215, 0.3));
      }

      #header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px 25px;
        background: var(--bg-secondary);
        color: var(--text-tertiary);
        border-bottom: 1px solid var(--accent-primary);
        transition: background-color 0.3s ease, border-color 0.3s ease;
      }
      #header .nav-left a {
        color: var(--text-tertiary);
        text-decoration: none;
        margin-right: 15px;
        font-weight: 500;
        transition: color 0.2s;
      }
      #header .nav-left a:hover {
        color: var(--accent-primary);
      }
      #header .nav-left span {
        margin-right: 15px;
        font-weight: 500;
        color: var(--accent-primary);
      }
      #header .nav-center {
        font-size: 20px;
        font-weight: 600;
        color: var(--accent-primary);
      }

      .icon-btn {
        border: none;
        cursor: pointer;
        height: 36px;
        width: 36px;
        background: transparent !important;
        color: var(--text-tertiary) !important;
        transition: color 0.3s, background-color 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
      }
      .icon-btn:hover {
        background: var(--shadow-accent-hover) !important;
        color: var(--accent-primary) !important;
      }
      .icon-btn svg {
        width: 16px;
        height: 16px;
        fill: currentColor;
      }
      #moon-icon, #sun-icon {
        display: none;
      }

      .container {
        max-width: 1000px;
        margin: 25px auto;
        padding: 0 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
      }
      .card {
        background: var(--bg-secondary);
        padding: 25px;
        border-radius: 6px;
        border: 1px solid var(--border-color);
        box-shadow: 0 0 15px var(--shadow-accent);
        margin-bottom: 25px;
        transition: background-color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
      }
      .card label {
        display: block;
        margin-bottom: 12px;
        font-weight: 500;
        font-size: 15px;
        color: var(--text-secondary);
      }

      #system-prompt-container {
        display: flex;
        flex-direction: column;
        gap: 15px;
      }
      .prompt-row {
        display: flex;
        align-items: center;
        gap: 10px;
      }
      select, button, textarea, input {
        font-family: 'Inter', sans-serif;
      }

      #systemPromptSelect {
        flex: 1;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        font-size: 15px;
        padding: 8px 12px;
        background: var(--bg-primary);
        color: var(--text-primary);
        transition: border-color 0.2s, box-shadow 0.2s;
      }
      #systemPromptSelect:focus {
        border-color: var(--accent-primary);
        outline: none;
        box-shadow: 0 0 0 2px var(--shadow-accent-hover);
      }

      #prompt-container {
        display: flex;
        flex-direction: column;
        gap: 10px;
      }

      #prompt {
        width: 100%;
        height: 100px;
        box-sizing: border-box;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        font-size: 15px;
        padding: 12px;
        resize: vertical;
        background: var(--bg-primary);
        color: var(--text-primary);
        transition: border-color 0.2s, box-shadow 0.2s;
      }
      #prompt:focus {
        border-color: var(--accent-primary);
        outline: none;
        box-shadow: 0 0 0 2px var(--shadow-accent-hover);
      }

      #drop-area {
        background: var(--bg-primary);
        border: 2px dashed var(--accent-primary);
        border-radius: 6px;
        color: var(--text-secondary);
        text-align: center;
        padding: 40px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: border-color 0.3s, background-color 0.3s;
        margin-bottom: 25px;
      }
      #drop-area.hover {
        border-color: var(--accent-tertiary);
        background-color: var(--bg-tertiary);
      }
      #drop-area p {
        margin: 10px 0 0 0;
        line-height: 1.5;
        font-size: 15px;
      }
      #drop-area svg {
        fill: var(--accent-primary);
        width: 48px;
        height: 48px;
        margin-bottom: 15px;
      }

      #buttons {
        padding: 10px 0;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
      }
      #output {
        display: none;
      }

      .btn {
        background: transparent;
        color: var(--accent-primary);
        border: 1px solid var(--accent-primary);
        font-size: 15px;
        font-weight: 600;
        border-radius: 4px;
        padding: 10px 18px;
        cursor: pointer;
        transition: background-color 0.2s, color 0.2s, box-shadow 0.2s;
        line-height: normal;
        text-transform: uppercase;
        letter-spacing: 0.5px;
      }
      .btn:hover {
        background: var(--accent-primary);
        color: var(--bg-primary);
        box-shadow: 0 0 10px var(--glow-accent);
      }

      #combine-copy.btn {
        background: var(--accent-primary);
        color: var(--bg-primary);
      }
      #combine-copy.btn:hover {
        background: var(--accent-secondary);
        border-color: var(--accent-secondary);
        box-shadow: 0 0 15px var(--glow-accent);
      }
      
      #generate-copy.btn {
        background: var(--accent-secondary);
        color: var(--bg-primary);
        border-color: var(--accent-secondary);
      }
      #generate-copy.btn:hover {
        background: var(--accent-tertiary);
        border-color: var(--accent-tertiary);
        box-shadow: 0 0 15px var(--glow-accent);
      }

      .collapsible-container {
        margin-bottom: 20px;
      }
      .collapsible-button {
        width: 100%;
        text-align: left;
        cursor: pointer;
        background-color: var(--bg-secondary);
        padding: 12px 18px;
        border-radius: 4px;
        font-weight: 500;
        font-size: 16px;
        border: 1px solid var(--border-color);
        color: var(--accent-primary);
        transition: background-color 0.2s, color 0.2s;
        display: flex;
        justify-content: space-between;
        align-items: center;
      }
      .collapsible-button:hover {
        background-color: var(--border-color);
        color: var(--accent-tertiary);
      }
      .collapsible-button .fas {
        transition: transform 0.3s ease;
      }
      .collapsible-button.active .fas {
        transform: rotate(180deg);
      }
      .collapsible-content {
        display: none;
        margin-top: 0;
        padding: 20px;
        border: 1px solid var(--border-color);
        border-top: none;
        border-radius: 0 0 4px 4px;
        background-color: var(--bg-primary);
      }

      .user-prompts-list {
        list-style: none;
        margin: 0;
        padding: 0;
      }

      .user-prompts-list li {
        display: flex;
        align-items: center;
        justify-content: space-between; 
        margin-bottom: 10px;
        background: var(--bg-primary);
        padding: 10px 15px;
        border-radius: 4px;
        border: 1px solid var(--border-color); 
      }
      .user-prompts-list li:hover {
          background-color: var(--bg-tertiary);
          cursor: pointer;
      }

      .user-prompts-list li strong {
        flex: 1;
        margin-right: 15px;
        font-weight: 500;
      }

      .btn-small {
        padding: 4px 8px;
        font-size: 12px;
        border-radius: 3px;
        cursor: pointer;
        transition: all 0.2s;
        text-transform: uppercase;
        font-weight: 500;
        margin-left: 5px;
      }

      .btn-edit {
        background: transparent;
        color: var(--accent-primary);
        border: 1px solid var(--accent-primary);
      }

      .btn-edit:hover {
        background: var(--accent-primary);
        color: var(--bg-primary);
        box-shadow: 0 0 5px var(--glow-accent);
      }

      .btn-delete {
        background: transparent;
        color: var(--error-color);
        border: 1px solid var(--error-color);
      }

      .btn-delete:hover {
        background: var(--error-color);
        color: var(--bg-primary);
        box-shadow: 0 0 5px rgba(255, 107, 107, 0.5);
      }

      .prompt-buttons {
        display: flex;
        gap: 5px;
      }

      .add-prompt-form h3 {
          color: var(--accent-primary);
          margin-top: 20px;
          margin-bottom: 15px;
          font-size: 18px;
          border-bottom: 1px solid var(--border-color);
          padding-bottom: 10px;
      }
      .add-prompt-form label {
        font-weight: 500;
        margin-bottom: 5px;
        display: block;
      }

      .add-prompt-form input[type="text"],
      .add-prompt-form input[type="password"],
      .add-prompt-form textarea {
        width: 100%;
        box-sizing: border-box;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        font-size: 15px;
        padding: 10px 12px;
        margin-bottom: 15px;
        background: var(--bg-primary); 
        color: var(--text-primary);
        transition: border-color 0.2s, box-shadow 0.2s;
      }
      .add-prompt-form input[type="text"]:focus,
      .add-prompt-form input[type="password"]:focus,
      .add-prompt-form textarea:focus {
          border-color: var(--accent-primary);
          outline: none;
          box-shadow: 0 0 0 2px var(--shadow-accent-hover);
      }

      .add-prompt-form button.btn { 
        width: 100%;
        padding: 10px 18px; 
        margin-top: 10px; 
      }
      .add-prompt-form .form-buttons {
          display: flex;
          gap: 10px; 
      }
    </style>
</head>
<body>

<div id="header">
  <div class="nav-left">
    <?php if ($isLoggedIn): ?>
      <span>Welcome, <?php echo htmlspecialchars($currentUser['email']); ?>!</span>
      <a href="logout.php">Logout</a>
    <?php else: ?>
      <a href="login.php">Login</a>
      <a href="signup.php">Sign Up</a>
    <?php endif; ?>
  </div>
  <div class="nav-center">
    FrogBytes's Prompt Helper
  </div>
  <button class="icon-btn" id="darkModeToggle">
    <svg id="moon-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
      <path d="M144.8 49.5C144.8 22.2 167 0 194.3 0s49.5 22.2 49.5 49.5v15.3c0 27.3-22.2 49.5-49.5 49.5s-49.5-22.2-49.5-49.5V49.5zM24 192c0-27.3 22.2-49.5 49.5-49.5h15.3c27.3 0 49.5 22.2 49.5 49.5s-22.2 49.5-49.5 49.5H73.5C46.2 241.5 24 219.3 24 192zm256 0c0-27.3 22.2-49.5 49.5-49.5H345c27.3 0 49.5 22.2 49.5 49.5s-22.2 49.5-49.5 49.5h-15.3c-27.3 0-49.5-22.2-49.5-49.5zM49.5 385.5C22.2 385.5 0 363.3 0 336s22.2-49.5 49.5-49.5h15.3c27.3 0 49.5 22.2 49.5 49.5s-22.2 49.5-49.5 49.5H49.5zM385.5 385.5c-27.3 0-49.5-22.2-49.5-49.5s22.2-49.5 49.5-49.5H401c27.3 0 49.5 22.2 49.5 49.5s-22.2 49.5-49.5 49.5h-15.5zM49.5 49.5C22.2 49.5 0 71.7 0 99s22.2 49.5 49.5 49.5h15.3c27.3 0 49.5-22.2 49.5-49.5S88.1 49.5 60.8 49.5H49.5zM385.5 49.5c-27.3 0-49.5 22.2-49.5 49.5s22.2 49.5 49.5 49.5H401c27.3 0 49.5-22.2 49.5-49.5S428.3 49.5 401 49.5h-15.5z"/>
    </svg>
    <svg id="sun-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
      <path d="M32 256c0-123.8 100.3-224 224-224s224 100.2 224 224-100.3 224-224 224S32 379.8 32 256zm224-64c-35.3 0-64 28.7-64 64s28.7 64 64 64 64-28.7 64-64-28.7-64-64-64z"/>
    </svg>
  </button>
</div>

<div class="container">
  <div class="card" id="system-prompt-container">
    <label for="systemPromptSelect">Select a system/user prompt (optional):</label>
    <div class="prompt-row">
      <select id="systemPromptSelect"></select>
    </div>
  </div>

  <div class="card" id="prompt-container">
    <label for="prompt">Enter a user prompt (will be placed after the selected prompt and above the file contents):</label>
    <textarea id="prompt"></textarea>
  </div>

  <input id="fileInput" type="file" multiple style="display: none;">

  <div class="card" id="drop-area">
    <svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 24 24">
      <path d="M19.35 10.04c.68 0 1.34.05 2 .13a9.994 9.994 0 0 0-1.98-6.88 9.99 9.99 0 0 0-7.87-2.22 7.48 7.48 0 0 0-7.38 9.09A7.52 7.52 0 0 0 7.52 20.9h10.93c3.24 0 5.85-2.61 5.85-5.85 0-3.09-2.4-5.61-5.43-5.99-.26-.03-.52-.04-.77-.02-.74.05-1.44.31-2 .7-.67.48-1.15 1.14-1.38 1.92-.25.82-.15 1.68.28 2.42.5.81 1.34 1.39 2.33 1.62 1.18.27 2.34-.21 2.98-1.18.59-.9.63-2.04.1-2.96-.5-.84-1.35-1.42-2.35-1.6a3.996 3.996 0 0 0-4.94 3.93c0 2.21 1.79 4 4 4H19.34z"></path>
    </svg>
    <p>Drag and drop your files here</p>
    <p><small>The first file will be considered the root file</small></p>
  </div>
  <div id="buttons">
    <button class="btn" id="clear-files">Clear Files</button>
    <button class="btn" id="generate-copy">Generate & Copy</button>
    <button class="btn" id="combine-copy">Combine & Copy</button>
  </div>

  <textarea id="output"></textarea>

  <div class="collapsible-container card">
    <button type="button" class="collapsible-button" id="togglePromptManagerBtn">
      Manage Your Custom Prompts <i class="fas fa-chevron-down"></i>
    </button>
    <div class="collapsible-content" id="promptManagerContent">
      <h3>Your Custom Prompts</h3>
      <ul class="user-prompts-list" id="userPromptsListUL">
      </ul>      
      <div class="add-prompt-form">
        <h3 id="promptFormTitle">Add a New Prompt</h3>
        <label for="newPromptTitle">Title</label>
        <input type="text" id="newPromptTitle" name="newPromptTitle">

        <label for="newPromptContent">Content</label>
        <textarea id="newPromptContent" name="newPromptContent" rows="4"></textarea>
        
        <div class="form-buttons">
            <button type="button" class="btn" id="saveUserPromptBtn">Add Prompt</button>
            <button type="button" class="btn" id="cancelEditPromptBtn" style="display: none;">Cancel Edit</button>
        </div>
      </div>

      <div class="add-prompt-form">
        <h3>Gemini API Configuration</h3>
        <label for="geminiApiKey">Gemini API Key (stored locally)</label>
        <input type="password" id="geminiApiKey" name="geminiApiKey" placeholder="Enter your Gemini API key">
        
        <div class="form-buttons">
            <button type="button" class="btn" id="saveApiKeyBtn">Save API Key</button>
            <button type="button" class="btn" id="clearApiKeyBtn">Clear API Key</button>
        </div>
        <p><small>Your API key is stored locally in your browser and never sent to our servers.</small></p>
      </div>
    </div>
  </div>
  
</div>

<script src="npm/pdfjs-dist%403.6.172/build/pdf.min.js"></script>
<script src="preset-prompts.js"></script>
<script src="prompt-sync.js"></script>
<script src="script.js"></script>

<script>
// Include the same JavaScript from index.htm here
window.prompts = [];
window.userPrompts = [];
let editingPromptId = null;
let isLightTheme = localStorage.getItem('frogbytes-theme') === 'light';

let togglePromptManagerBtn;
let promptManagerContent;
let userPromptsListUL;
let newPromptTitleInput;
let newPromptContentInput;
let saveUserPromptBtn;
let cancelEditPromptBtn;
let promptFormTitle;
let darkModeToggle;
let moonIcon;
let sunIcon;
let geminiApiKeyInput;
let saveApiKeyBtn;
let clearApiKeyBtn;

// Include all the JavaScript functions from the original index.htm
function renderUserPrompts() {
  console.log('Rendering user prompts:', window.userPrompts);
  userPromptsListUL.innerHTML = '';
  if (window.userPrompts.length === 0) {
      userPromptsListUL.innerHTML = '<li>No custom prompts yet. Add one below!</li>';
      return;
  }
  window.userPrompts.forEach(prompt => {
    const li = document.createElement('li');
    li.innerHTML = `<strong>${prompt.title}</strong>
      <div class="prompt-buttons">
        <button class="btn-small btn-edit" onclick="editUserPrompt(${prompt.id})">Edit</button>
        <button class="btn-small btn-delete" onclick="deleteUserPrompt(${prompt.id})">Delete</button>
      </div>`;
    userPromptsListUL.appendChild(li);
  });
}

function resetPromptForm() {
  editingPromptId = null;
  newPromptTitleInput.value = '';
  newPromptContentInput.value = '';
  promptFormTitle.textContent = 'Add a New Prompt';
  saveUserPromptBtn.textContent = 'Add Prompt';
  cancelEditPromptBtn.style.display = 'none';
}

function saveUserPrompt() {
  const title = newPromptTitleInput.value.trim();
  const content = newPromptContentInput.value.trim();

  if (!title || !content) {
    alert('Please enter both title and content for the prompt.');
    return;
  }

  if (editingPromptId !== null) {
    const promptIndex = window.userPrompts.findIndex(p => p.id === editingPromptId);
    if (promptIndex > -1) {
      window.userPrompts[promptIndex] = { 
        ...window.userPrompts[promptIndex], 
        title, 
        content 
      };
      
      // Save to server if logged in
      if (window.promptSync.isLoggedIn) {
        window.promptSync.saveToServer(window.userPrompts[promptIndex])
          .then(result => {
            if (!result.success) {
              console.error('Failed to save prompt to server:', result.message);
            }
          })
          .catch(error => {
            console.error('Error saving prompt to server:', error);
          });
      }
    }
  } else {
    const newId = window.userPrompts.length > 0 ? Math.max(...window.userPrompts.map(p => p.id)) + 1 : 1;
    const newPrompt = { id: newId, title, content };
    window.userPrompts.push(newPrompt);
    
    // Save to server if logged in
    if (window.promptSync.isLoggedIn) {
      window.promptSync.saveToServer(newPrompt)
        .then(result => {
          if (result.success && result.prompt) {
            // Update the prompt with server ID
            newPrompt.serverId = parseInt(result.prompt.id);
            saveUserPromptsToStorage();
          } else {
            console.error('Failed to save prompt to server:', result.message);
          }
        })
        .catch(error => {
          console.error('Error saving prompt to server:', error);
        });
    }
  }
  
  saveUserPromptsToStorage();
  renderUserPrompts();
  populateSystemPromptSelect();
  resetPromptForm();
}

window.editUserPrompt = function(id) {
  const promptToEdit = window.userPrompts.find(p => p.id === id);
  if (promptToEdit) {
    editingPromptId = id;
    newPromptTitleInput.value = promptToEdit.title;
    newPromptContentInput.value = promptToEdit.content;
    promptFormTitle.textContent = 'Edit Prompt';
    saveUserPromptBtn.textContent = 'Save Changes';
    cancelEditPromptBtn.style.display = 'inline-block';
    
    if (!promptManagerContent.style.display || promptManagerContent.style.display === 'none') {
      togglePromptManagerBtn.click();
    }
    newPromptTitleInput.focus();
  }
};

window.deleteUserPrompt = function(id) {
  if (confirm('Are you sure you want to delete this prompt?')) {
    const promptIndex = window.userPrompts.findIndex(p => p.id === id);
    const prompt = promptIndex > -1 ? window.userPrompts[promptIndex] : null;
    
    // Delete from local array
    window.userPrompts = window.userPrompts.filter(p => p.id !== id);
    
    // Delete from server if logged in and has server ID
    if (window.promptSync.isLoggedIn && prompt && prompt.serverId) {
      window.promptSync.deleteFromServer(prompt)
        .then(result => {
          if (!result.success) {
            console.error('Failed to delete prompt from server:', result.message);
          }
        })
        .catch(error => {
          console.error('Error deleting prompt from server:', error);
        });
    }
    
    saveUserPromptsToStorage();
    renderUserPrompts();
    populateSystemPromptSelect();
  }
};

function populateSystemPromptSelect() {
  const select = document.getElementById('systemPromptSelect');
  if (!select) return;
  
  select.innerHTML = '<option value="">-- Select a prompt --</option>';
    
  if (window.PresetPromptManager) {
    const presetPrompts = window.PresetPromptManager.getAll();
    presetPrompts.forEach(prompt => {
      const option = document.createElement('option');
      option.value = `preset-${prompt.id}`;
      option.textContent = `[preset] ${prompt.title}`;
      option.dataset.content = prompt.content;
      select.appendChild(option);
    });
  }
  
  window.userPrompts.forEach(prompt => {
    const option = document.createElement('option');
    option.value = `user-${prompt.id}`;
    option.textContent = `[custom] ${prompt.title}`;
    option.dataset.content = prompt.content;
    select.appendChild(option);
  });
}

function loadUserPrompts() {
  try {
    const saved = localStorage.getItem('frogbytes-user-prompts');
    if (saved) {
      window.userPrompts = JSON.parse(saved);
    } else {
      window.userPrompts = [
        { id: 1, title: "Code Reviewer", content: "Review this code for bugs, performance issues, and best practices. Provide specific suggestions for improvement." },
        { id: 2, title: "Documentation Helper", content: "Generate comprehensive documentation for this code including usage examples and API reference." },
        { id: 3, title: "Bug Finder", content: "Analyze this code to find potential bugs, security vulnerabilities, and edge cases that might cause issues." }
      ];
      saveUserPromptsToStorage();
    }
  } catch (e) {
    console.error('Error loading user prompts from localStorage:', e);
    window.userPrompts = [];
  }
}

function saveUserPromptsToStorage() {
  try {
    localStorage.setItem('frogbytes-user-prompts', JSON.stringify(window.userPrompts));
  } catch (e) {
    console.error('Error saving user prompts to localStorage:', e);
  }
}

function saveGeminiApiKey() {
  const apiKey = geminiApiKeyInput.value.trim();
  if (!apiKey) {
    alert('Please enter a valid API key.');
    return;
  }
  
  try {
    localStorage.setItem('frogbytes-gemini-api-key', apiKey);
    alert('API key saved successfully!');
    geminiApiKeyInput.value = '';
  } catch (e) {
    console.error('Error saving API key:', e);
    alert('Error saving API key. Please try again.');
  }
}

function clearGeminiApiKey() {
  if (confirm('Are you sure you want to clear the saved API key?')) {
    try {
      localStorage.removeItem('frogbytes-gemini-api-key');
      alert('API key cleared successfully!');
      geminiApiKeyInput.value = '';
    } catch (e) {
      console.error('Error clearing API key:', e);
    }
  }
}

function applyTheme() {
  if (isLightTheme) {
    document.body.classList.add('light-theme');
    moonIcon.style.display = 'block';
    sunIcon.style.display = 'none';
  } else {
    document.body.classList.remove('light-theme');
    moonIcon.style.display = 'none';
    sunIcon.style.display = 'block';
  }
}

function toggleTheme() {
  isLightTheme = !isLightTheme;
  localStorage.setItem('frogbytes-theme', isLightTheme ? 'light' : 'dark');
  applyTheme();
}

function initializePromptManager() {
  renderUserPrompts();
  populateSystemPromptSelect();
}

function initCollapsible() {
  if (togglePromptManagerBtn && promptManagerContent) {
    promptManagerContent.style.display = 'none';
    togglePromptManagerBtn.classList.remove('active');
    
    togglePromptManagerBtn.addEventListener('click', (e) => {
      e.preventDefault();
      const isActive = togglePromptManagerBtn.classList.toggle('active');
      
      if (isActive) {
        promptManagerContent.style.display = 'block';
      } else {
        promptManagerContent.style.display = 'none';
      }
    });
  }
}

document.addEventListener('DOMContentLoaded', () => {
  togglePromptManagerBtn = document.getElementById('togglePromptManagerBtn');
  promptManagerContent = document.getElementById('promptManagerContent');
  userPromptsListUL = document.getElementById('userPromptsListUL');
  newPromptTitleInput = document.getElementById('newPromptTitle');
  newPromptContentInput = document.getElementById('newPromptContent');
  saveUserPromptBtn = document.getElementById('saveUserPromptBtn');
  cancelEditPromptBtn = document.getElementById('cancelEditPromptBtn');
  promptFormTitle = document.getElementById('promptFormTitle');
  darkModeToggle = document.getElementById('darkModeToggle');
  moonIcon = document.getElementById('moon-icon');
  sunIcon = document.getElementById('sun-icon');
  geminiApiKeyInput = document.getElementById('geminiApiKey');
  saveApiKeyBtn = document.getElementById('saveApiKeyBtn');
  clearApiKeyBtn = document.getElementById('clearApiKeyBtn');
  
  // Set login status for prompt sync
  window.promptSync.setLoginStatus(<?php echo $isLoggedIn ? 'true' : 'false'; ?>);
  
  loadUserPrompts();
  applyTheme();
  initCollapsible();
    
  if (saveUserPromptBtn) {
    saveUserPromptBtn.addEventListener('click', saveUserPrompt);
  }
  if (cancelEditPromptBtn) {
    cancelEditPromptBtn.addEventListener('click', resetPromptForm);
  }
  if (darkModeToggle) {
    darkModeToggle.addEventListener('click', toggleTheme);
  }
  if (saveApiKeyBtn) {
    saveApiKeyBtn.addEventListener('click', saveGeminiApiKey);
  }
  if (clearApiKeyBtn) {
    clearApiKeyBtn.addEventListener('click', clearGeminiApiKey);
  }
  
  setTimeout(() => {
    initializePromptManager();
    
    // Sync prompts with server if logged in
    if (window.promptSync.isLoggedIn) {
      console.log('User is logged in, syncing prompts with server...');
      window.promptSync.syncPrompts();
    }
  }, 100);
});

</script>

</body>
</html>
