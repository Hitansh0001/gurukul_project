// Frontend JS to interact with backend API and render UI
(function () {
  const $ = (sel) => document.querySelector(sel);
  const $$ = (sel) => document.querySelectorAll(sel);

  const state = {
    baseUrl: localStorage.getItem('api_base_url') || 'http://localhost:8000',
  };

  // Elements
  const inputEl = $('#user-input');
  const contextEl = $('#context-input');
  const includeYouTubeEl = $('#include-youtube');
  const submitBtn = $('#submit-btn');
  const loadingEl = $('#loading');
  const textResponseEl = $('#text-response');
  const responseTextEl = $('#text-response .response-text');
  const timestampEl = $('#text-response .timestamp');
  const recsContainer = $('#youtube-recommendations');
  const recsGrid = $('#recommendations-grid');
  const apiEndpointEl = $('#api-endpoint');
  const maxResultsEl = $('#max-results');
  const configToggle = $('#config-toggle');
  const configPanel = $('#config-panel');
  const apiStatus = $('#api-status');
  const errorToast = $('#error-toast');
  const successToast = $('#success-toast');
  const errorToastMsg = $('#error-toast .toast-message');
  const successToastMsg = $('#success-toast .toast-message');

  // Initialize config UI
  apiEndpointEl.value = state.baseUrl;
  apiEndpointEl.addEventListener('change', () => {
    state.baseUrl = apiEndpointEl.value.trim().replace(/\/$/, '');
    localStorage.setItem('api_base_url', state.baseUrl);
    pingApi();
  });

  configToggle.addEventListener('click', () => {
    configPanel.classList.toggle('open');
  });

  // API helpers
  async function pingApi() {
    try {
      const res = await fetch(`${state.baseUrl}/health`);
      if (!res.ok) throw new Error('API not reachable');
      setApiStatus('connected');
    } catch (e) {
      setApiStatus('error');
    }
  }

  function setApiStatus(status) {
    const dot = apiStatus.querySelector('.status-dot');
    dot.classList.remove('connected', 'error');
    if (status === 'connected') {
      dot.classList.add('connected');
      apiStatus.querySelector('span:last-child').textContent = 'API Status: Connected';
    } else if (status === 'error') {
      dot.classList.add('error');
      apiStatus.querySelector('span:last-child').textContent = 'API Status: Error';
    } else {
      apiStatus.querySelector('span:last-child').textContent = 'API Status: Unknown';
    }
  }

  function show(el) { el.classList.remove('hidden'); }
  function hide(el) { el.classList.add('hidden'); }

  function showError(msg) {
    errorToastMsg.textContent = msg;
    errorToast.classList.remove('hidden');
    setTimeout(() => hide(errorToast), 4000);
  }

  function showSuccess(msg) {
    successToastMsg.textContent = msg;
    successToast.classList.remove('hidden');
    setTimeout(() => hide(successToast), 2000);
  }

  window.hideToast = function () {
    hide(errorToast);
    hide(successToast);
  };

  window.copyResponse = async function () {
    try {
      await navigator.clipboard.writeText(responseTextEl.textContent);
      showSuccess('Response copied to clipboard');
    } catch (e) {
      showError('Failed to copy to clipboard');
    }
  };

  function renderRecommendations(items) {
    recsGrid.innerHTML = '';
    items.forEach((item) => {
      const card = document.createElement('a');
      card.href = item.url;
      card.target = '_blank';
      card.rel = 'noopener noreferrer';
      card.className = 'video-card';
      card.innerHTML = `
        <img class="video-thumbnail" src="${item.thumbnail_url}" alt="${item.title}">
        <div class="video-info">
          <div class="video-title">${item.title}</div>
          <div class="video-channel">${item.channel_name || ''}</div>
        </div>
      `;
      recsGrid.appendChild(card);
    });
  }

  async function handleSubmit() {
    const text = inputEl.value.trim();
    const context = contextEl.value.trim();
    const includeYouTube = includeYouTubeEl.checked;
    const maxResults = parseInt(maxResultsEl.value || '6', 10);

    if (!text) {
      showError('Please enter some text');
      return;
    }

    hide(textResponseEl);
    hide(recsContainer);
    show(loadingEl);
    submitBtn.disabled = true;

    try {
      if (includeYouTube) {
        const res = await fetch(`${state.baseUrl}/api/combined-response`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ text, context })
        });
        if (!res.ok) throw new Error('Request failed');
        const data = await res.json();
        responseTextEl.textContent = data.text_response.response;
        timestampEl.textContent = new Date(data.text_response.timestamp).toLocaleString();
        show(textResponseEl);
        renderRecommendations(data.youtube_recommendations.slice(0, maxResults));
        show(recsContainer);
      } else {
        const res = await fetch(`${state.baseUrl}/api/process-text`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ text, context })
        });
        if (!res.ok) throw new Error('Request failed');
        const data = await res.json();
        responseTextEl.textContent = data.response;
        timestampEl.textContent = new Date(data.timestamp).toLocaleString();
        show(textResponseEl);
      }
    } catch (e) {
      showError(e.message || 'Something went wrong');
    } finally {
      hide(loadingEl);
      submitBtn.disabled = false;
    }
  }

  submitBtn.addEventListener('click', handleSubmit);

  // Helpers visible from HTML
  window.showApiDocs = function () {
    alert('API Endpoints:\n' +
      '/health - GET\n' +
      '/api/process-text - POST { text, context? }\n' +
      '/api/youtube-recommendations - POST { query, max_results? }\n' +
      '/api/combined-response - POST { text, context? }');
  };

  window.showHelp = function () {
    alert('Enter your text, optionally provide context, and click "Process Text".\nUse the gear icon to configure API endpoint and max YouTube results.');
  };

  // Initial ping
  pingApi();
})();
