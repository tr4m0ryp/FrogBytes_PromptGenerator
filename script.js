// script.js

// File handling and main app functionality
// Theme management and prompt dropdown population is handled by index.htm

// Internal storage of dropped files
let files = [];
let rootFile = null;

/**
 * The merged prompts (system + user) come from index.php via window.prompts.
 * We'll store them locally as "allPrompts".
 */
const allPrompts = window.prompts || [];

// Grab needed DOM elements (theme elements handled by index.htm)
const dropArea = document.getElementById('drop-area');
const promptField = document.getElementById('prompt');
const outputField = document.getElementById('output');
const combineButton = document.getElementById('combine-copy');
const generateButton = document.getElementById('generate-copy');
const clearFilesButton = document.getElementById('clear-files');
const systemPromptSelect = document.getElementById('systemPromptSelect');

// Hidden file input (for mobile/touch)
const fileInput = document.getElementById('fileInput');

/**
 * Initialize the page: update the drop-area UI, etc.
 * Note: populateSystemPromptSelect is handled by index.htm
 */
function init() {
  updateDropAreaMessage();
}

// Initialize when DOM is loaded (but let index.htm handle theme and dropdown)
document.addEventListener('DOMContentLoaded', () => {
  init();
});

/**
 * Process dropped or selected files, reading PDF or text, and storing them.
 */
function handleFiles(fileList) {
  Array.from(fileList).forEach((fileObj) => {
    if (!rootFile) {
      // The very first file is considered the root
      rootFile = fileObj.name;
    }
    let fileName = fileObj.name.toLowerCase();
    if (fileName.endsWith('.pdf')) {
      // Read PDF as ArrayBuffer
      let reader = new FileReader();
      reader.onload = async (event) => {
        let arrayBuffer = event.target.result;
        // Parse PDF text with PDF.js
        let pdfText = await parsePdf(arrayBuffer);
        files.push({ filePath: fileObj.name, content: pdfText });
        updateDropAreaMessage();
      };
      reader.readAsArrayBuffer(fileObj);
    } else {
      // Assume plain text or code; read as text
      let reader = new FileReader();
      reader.onload = (event) => {
        let content = event.target.result;
        files.push({ filePath: fileObj.name, content: content });
        updateDropAreaMessage();
      };
      reader.readAsText(fileObj, 'utf-8');
    }
  });
}

// Drag-and-drop events on the drop area
dropArea.addEventListener('dragover', (e) => {
  e.preventDefault();
  dropArea.classList.add('hover');
  e.dataTransfer.dropEffect = 'copy';
});

dropArea.addEventListener('dragleave', () => {
  dropArea.classList.remove('hover');
});

dropArea.addEventListener('drop', (e) => {
  e.preventDefault();
  dropArea.classList.remove('hover');
  const droppedFiles = e.dataTransfer.files;
  if (droppedFiles.length === 0) return;
  handleFiles(droppedFiles);
});

// Allow click on the drop area to open file dialog (mobile-friendly)
dropArea.addEventListener('click', () => {
  fileInput.click();
});

// Listen for changes on the hidden file input
fileInput.addEventListener('change', (e) => {
  if (e.target.files.length === 0) return;
  handleFiles(e.target.files);
});

/**
 * Parse PDF using PDF.js and return text content.
 */
async function parsePdf(arrayBuffer) {
  try {
    const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
    let allText = [];
    for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
      let page = await pdf.getPage(pageNum);
      let textContent = await page.getTextContent();
      // Join all text items for this page
      let pageText = textContent.items.map(item => item.str).join(' ');
      allText.push(pageText);
    }
    return allText.join('\n\n');
  } catch (err) {
    console.error('PDF parsing error:', err);
    return '<< PDF parsing failed >>';
  }
}

/**
 * Update the drop area message depending on whether files have been dropped.
 */
function updateDropAreaMessage() {
  if (!rootFile) {
    dropArea.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
        <path d="M19.35 10.04c.68 0 1.34.05 2 .13a9.994 9.994 0 0 0-1.98-6.88 9.99 9.99 0 0 0-7.87-2.22 7.48 7.48 0 0 0-7.38 9.09A7.52 7.52 0 0 0 7.52 20.9h10.93c3.24 0 5.85-2.61 5.85-5.85 0-3.09-2.4-5.61-5.43-5.99-.26-.03-.52-.04-.77-.02-.74.05-1.44.31-2 .7-.67.48-1.15 1.14-1.38 1.92-.25.82-.15 1.68.28 2.42.5.81 1.34 1.39 2.33 1.62 1.18.27 2.34-.21 2.98-1.18.59-.9.63-2.04.1-2.96-.5-.84-1.35-1.42-2.35-1.6a3.996 3.996 0 0 0-4.94 3.93c0 2.21 1.79 4 4 4H19.34z"/>
      </svg>
      <p>Drag and drop your files here</p>
      <p><small>The first file will be considered the root file</small></p>
    `;
  } else {
    let list = files.map(f => `• ${f.filePath}`).join('<br>');
    dropArea.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
        <path d="M19.35 10.04c.68 0 1.34.05 2 .13a9.994 9.994 0 0 0-1.98-6.88 9.99 9.99 0 0 0-7.87-2.22 7.48 7.48 0 0 0-7.38 9.09A7.52 7.52 0 0 0 7.52 20.9h10.93c3.24 0 5.85-2.61 5.85-5.85 0-3.09-2.4-5.61-5.43-5.99-.26-.03-.52-.04-.77-.02-.74.05-1.44.31-2 .7-.67.48-1.15 1.14-1.38 1.92-.25.82-.15 1.68.28 2.42.5.81 1.34 1.39 2.33 1.62 1.18.27 2.34-.21 2.98-1.18.59-.9.63-2.04.1-2.96-.5-.84-1.35-1.42-2.35-1.6a3.996 3.996 0 0 0-4.94 3.93c0 2.21 1.79 4 4 4H19.34z"/>
      </svg>
      <p><strong>Root File:</strong> ${rootFile}</p>
      <p><strong>Files:</strong><br>${list}</p>
    `;
  }
}

/**
 * When "Combine & Copy" is clicked, we gather:
 * 1) The selected prompt (if any),
 * 2) The user's prompt text,
 * 3) The content of each file,
 * Then merge them into one string and copy it to clipboard.
 */
combineButton.addEventListener('click', () => {
  const userPrompt = promptField.value.trim();
  const selectedValue = systemPromptSelect.value;

  let selectedPromptContent = "";
  if (selectedValue !== "") {
    if (selectedValue.startsWith('preset-')) {
      // Handle preset prompts
      const promptId = selectedValue.replace('preset-', '');
      if (window.PresetPromptManager) {
        const prompt = window.PresetPromptManager.getById(promptId);
        if (prompt) {
          selectedPromptContent = prompt.content.trim();
        }
      }    } else if (selectedValue.startsWith('user-')) {
      // Handle user prompts
      const promptId = parseInt(selectedValue.replace('user-', ''));
      if (window.userPrompts && Array.isArray(window.userPrompts)) {
        const prompt = window.userPrompts.find(p => p.id === promptId);
        if (prompt) {
          selectedPromptContent = prompt.content.trim();
        }
      }
    }
  }

  let combined = "";
  if (selectedPromptContent) {
    combined += selectedPromptContent + "\n\n";
  }
  if (userPrompt) {
    combined += userPrompt + "\n\n";
  }

  for (let i = 0; i < files.length; i++) {
    const { filePath, content } = files[i];
    if (filePath.toLowerCase().endsWith('.md')) {
      // Markdown file
      combined += `-${filePath}\n${content}\n`;
    } else {
      // For PDF, .js, .txt, .html, etc.
      combined += `- ${filePath}\n\`\`\`\n${content}\n\`\`\`\n\n`;
    }
  }

  outputField.value = combined;

  // Modern async clipboard API
  navigator.clipboard.writeText(combined).then(() => {
    alert('Combined text copied to clipboard.');
  }).catch(() => {
    alert('Could not copy to clipboard, please copy manually.');
  });
});

/**
 * Clears out all stored files and resets the root file.
 */
clearFilesButton.addEventListener('click', () => {
  files = [];
  rootFile = null;
  updateDropAreaMessage();
});

/**
 * Generate & Copy functionality using Gemini API
 */
generateButton.addEventListener('click', async () => {
  const userPrompt = promptField.value.trim();
  
  if (!userPrompt) {
    alert('Please enter a prompt in the textarea first.');
    return;
  }

  // Check if API key is configured
  const apiKey = localStorage.getItem('frogbytes-gemini-api-key');
  if (!apiKey) {
    alert('Please configure your Gemini API key in the "Manage Your Custom Prompts" section first.');
    return;
  }

  try {
    // Show loading state
    generateButton.textContent = 'Generating...';
    generateButton.disabled = true;

    // Call Gemini API to improve the prompt
    const improvedPrompt = await generateWithGemini(userPrompt);
    
    // Update the textarea with the improved prompt
    promptField.value = improvedPrompt;

    // Now proceed with the combine and copy logic using the improved prompt
    const selectedValue = systemPromptSelect.value;

    let selectedPromptContent = "";
    if (selectedValue !== "") {
      if (selectedValue.startsWith('preset-')) {
        // Handle preset prompts
        const promptId = selectedValue.replace('preset-', '');
        if (window.PresetPromptManager) {
          const prompt = window.PresetPromptManager.getById(promptId);
          if (prompt) {
            selectedPromptContent = prompt.content.trim();
          }
        }
      } else if (selectedValue.startsWith('user-')) {
        // Handle user prompts
        const promptId = parseInt(selectedValue.replace('user-', ''));
        if (window.userPrompts && Array.isArray(window.userPrompts)) {
          const prompt = window.userPrompts.find(p => p.id === promptId);
          if (prompt) {
            selectedPromptContent = prompt.content.trim();
          }
        }
      }
    }

    let combined = "";
    if (selectedPromptContent) {
      combined += selectedPromptContent + "\n\n";
    }
    if (improvedPrompt) {
      combined += improvedPrompt + "\n\n";
    }

    for (let i = 0; i < files.length; i++) {
      const { filePath, content } = files[i];
      if (filePath.toLowerCase().endsWith('.md')) {
        // Markdown file
        combined += `-${filePath}\n${content}\n`;
      } else {
        // For PDF, .js, .txt, .html, etc.
        combined += `- ${filePath}\n\`\`\`\n${content}\n\`\`\`\n\n`;
      }
    }

    outputField.value = combined;

    // Modern async clipboard API
    await navigator.clipboard.writeText(combined);
    alert('Prompt improved and combined text copied to clipboard!');

  } catch (error) {
    console.error('Error generating with Gemini:', error);
    alert(`Error: ${error.message}`);
  } finally {
    // Restore button state
    generateButton.textContent = 'Generate & Copy';
    generateButton.disabled = false;
  }
});

async function generateWithGemini(prompt) {
  const apiKey = localStorage.getItem('frogbytes-gemini-api-key');
  if (!apiKey) {
    throw new Error('Gemini API key not found. Please configure your API key in the settings.');
  }

  const url = `https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=${apiKey}`;
  
  const requestBody = {
    contents: [{
      parts: [{
        text: `Please rewrite and improve the following prompt to make it clearer, more specific, and more effective for getting better results from an AI assistant. Keep the core intent but make it more professional and detailed:\n\n${prompt}`
      }]
    }],
    generationConfig: {
      temperature: 0.7,
      topK: 40,
      topP: 0.95,
      maxOutputTokens: 1024,
    }
  };

  const response = await fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(requestBody)
  });

  if (!response.ok) {
    const errorData = await response.json().catch(() => ({}));
    throw new Error(`Gemini API error: ${response.status} - ${errorData.error?.message || 'Unknown error'}`);
  }

  const data = await response.json();
  
  if (!data.candidates || !data.candidates[0] || !data.candidates[0].content) {
    throw new Error('Invalid response from Gemini API');
  }

  return data.candidates[0].content.parts[0].text;
}
