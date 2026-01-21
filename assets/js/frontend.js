(function () {
  function escHtml(str) {
    const div = document.createElement("div");
    div.textContent = str == null ? "" : String(str);
    return div.innerHTML;
  }

  function buildLauncherIcon(launcher) {
    if (launcher.type === "image" && launcher.imageUrl) {
      return `<img class="mlx-chat-launcher-img" src="${escHtml(launcher.imageUrl)}" alt="" />`;
    }
    // Dashicon class
    const cls = launcher.dashicon ? launcher.dashicon : "dashicons-format-chat";
    return `<span class="dashicons ${escHtml(cls)}" aria-hidden="true"></span>`;
  }

  function applyPosition(root, mode, customPos, customPosMobile) {
    root.style.left = "";
    root.style.right = "";
    root.style.bottom = "";
    root.style.top = "";

    if (mode === "left") {
      root.style.left = "30px";
      root.style.bottom = "30px";
      return;
    }
    if (mode === "custom" && customPos) {
      // expects "left:10px; bottom:10px;"
      root.setAttribute("style", root.getAttribute("style") + ";" + customPos);
      if (window.matchMedia("(max-width: 480px)").matches) {
        root.setAttribute(
          "style",
          root.getAttribute("style") + ";" + customPosMobile,
        );
      }
      return;
    }
    // right default
    root.style.right = "30px";
    root.style.bottom = "30px";
  }

  function applyColors(root, colors) {
    root.style.setProperty("--mlx-primary", colors.primary || "#25D366");
    root.style.setProperty("--mlx-text", colors.text || "#111");
    root.style.setProperty("--mlx-panel-bg", colors.panelBg || "#fff");
  }

  function applyLauncherStyle(root, ls) {
    if (!ls) return;
    root.style.setProperty(
      "--mlx-launcher-size-width",
      (ls.width != null ? ls.width : 56) + "px",
    );
    root.style.setProperty(
      "--mlx-launcher-size-height",
      (ls.height != null ? ls.height : 56) + "px",
    );
    root.style.setProperty(
      "--mlx-launcher-size-width-mobile",
      (ls.width != null ? ls.width_mobile : 32) + "px",
    );
    root.style.setProperty(
      "--mlx-launcher-size-height-mobile",
      (ls.height != null ? ls.height_mobile : 32) + "px",
    );
    root.style.setProperty("--mlx-launcher-bg", ls.bgColor || "");
    root.style.setProperty("--mlx-launcher-fg", ls.iconColor || "");
    root.style.setProperty(
      "--mlx-launcher-bw",
      (ls.borderWidth != null ? ls.borderWidth : 0) + "px",
    );
    root.style.setProperty("--mlx-launcher-bc", ls.borderColor || "");
    root.style.setProperty(
      "--mlx-launcher-br",
      (ls.borderRadius != null ? ls.borderRadius : 999) + "px",
    );
  }

  function createUI(data) {
    const root = document.getElementById("mlx-chat-box-root");
    if (!root) return;

    root.innerHTML = `
      <div class="mlx-chat-overlay" hidden></div>

      <button type="button" class="mlx-chat-launcher" aria-label="${escHtml(data.texts.header)}">
        ${buildLauncherIcon(data.launcher)}
      </button>

      <div class="mlx-chat-panel" role="dialog" aria-modal="false" aria-label="${escHtml(data.texts.header)}" hidden>
        <div class="mlx-chat-header">
          <div class="mlx-chat-title">${escHtml(data.texts.header)}</div>
          <button type="button" class="mlx-chat-close" aria-label="Close">×</button>
        </div>

        <div class="mlx-chat-body">
          <input class="mlx-chat-search" name="mlx-chat-search" type="search" placeholder="${escHtml(data.texts.search)}" />
          <div class="mlx-chat-list" role="list"></div>
          <div class="mlx-chat-offline" hidden>${escHtml(data.texts.offline)}</div>

          <div class="mlx-chat-confirm" ${data.requireConfirm ? "" : "hidden"}>
            <label class="mlx-chat-confirm-label">
              <input type="checkbox" class="mlx-chat-confirm-check" />
                <span>${escHtml(data.texts.confirm || "My question/answer is not listed here.")}</span>
            </label>
          </div>
        </div>

        <div class="mlx-chat-footer">
          <a class="mlx-chat-contact" href="#" target="_blank" rel="noopener noreferrer">
            ${escHtml(data.texts.contact)}
          </a>
        </div>
      </div>
    `;

    applyPosition(
      root,
      data.positionMode,
      data.customPos,
      data.customPosMobile,
    );
    applyColors(root, data.colors);
    applyLauncherStyle(root, data.launcherStyle);

    const launcher = root.querySelector(".mlx-chat-launcher");
    const panel = root.querySelector(".mlx-chat-panel");
    const closeBtn = root.querySelector(".mlx-chat-close");
    const list = root.querySelector(".mlx-chat-list");
    const search = root.querySelector(".mlx-chat-search");
    const offline = root.querySelector(".mlx-chat-offline");
    const contact = root.querySelector(".mlx-chat-contact");
    const overlay = root.querySelector(".mlx-chat-overlay");

    const confirmWrap = root.querySelector(".mlx-chat-confirm");
    const confirmCheck = root.querySelector(".mlx-chat-confirm-check");

    let isOpen = false;

    function setContactEnabled(enabled) {
      if (enabled) {
        contact.classList.remove("is-disabled");
        contact.removeAttribute("aria-disabled");
        contact.removeAttribute("tabindex");
      } else {
        contact.classList.add("is-disabled");
        contact.setAttribute("aria-disabled", "true");
        contact.setAttribute("tabindex", "-1");
      }
    }

    const online = !!data.isOnline;
    const baseUrl = data.contactUrl || "";

    function computeContactUrl() {
      if (!baseUrl) return "";
      const msg = data.productMessage || "";
      if (!msg) return baseUrl;
      const sep = baseUrl.indexOf("?") >= 0 ? "&" : "?";
      return baseUrl + sep + "text=" + encodeURIComponent(msg);
    }

    function refreshContactState() {
      // Offline => always disabled
      if (!online) {
        offline.hidden = false;
        contact.href = "#";
        setContactEnabled(false);
        confirmWrap.remove();
        return;
      }

      const url = computeContactUrl();
      contact.href = url || "#";

      // Show/hide confirmation checkbox
      if (confirmWrap) {
        confirmWrap.hidden = !(data.requireConfirm && url);
      }

      // No destination => disabled
      if (!url) {
        setContactEnabled(false);
        return;
      }

      // If confirmation required => enable only when checked
      if (data.requireConfirm && confirmCheck) {
        setContactEnabled(!!confirmCheck.checked);
        return;
      }

      // Otherwise enabled
      setContactEnabled(true);
    }

    if (!online) {
      // prevent click when offline
      contact.addEventListener("click", (e) => e.preventDefault());
    } else {
      // still prevent if disabled
      contact.addEventListener("click", (e) => {
        if (contact.classList.contains("is-disabled")) e.preventDefault();
      });
    }

    if (confirmCheck) {
      confirmCheck.addEventListener("change", refreshContactState);
    }

    refreshContactState();

    function setOpen(next) {
      isOpen = next;
      panel.hidden = !isOpen;
      if (overlay) overlay.hidden = !isOpen;

      // Reset confirmation on every open
      if (isOpen && data.requireConfirm && confirmCheck) {
        confirmCheck.checked = false;
        refreshContactState();
      }

      launcher.setAttribute("aria-expanded", isOpen ? "true" : "false");
      if (isOpen) search && search.focus();
    }

    // External triggers
    const triggerSelector = (data.triggerSelector || "").trim();
    if (triggerSelector) {
      document.addEventListener("click", (e) => {
        const t = e.target;
        if (!t) return;

        const match = t.closest(triggerSelector);
        if (!match) return;

        // If it's a link/button that navigates, prevent it (user expects opening panel)
        if (match.tagName === "A") e.preventDefault();

        setOpen(true);
      });
    }

    // Overlay click closes
    if (overlay) {
      overlay.addEventListener("click", () => setOpen(false));
    }

    function buildContactUrl() {
      const base = data.contactUrl || "";
      if (!base) return "";

      // WhatsApp supports ?text=, custom url may ignore it (fine).
      const msg = data.productMessage || "";
      if (!msg) return base;

      const sep = base.indexOf("?") >= 0 ? "&" : "?";
      return base + sep + "text=" + encodeURIComponent(msg);
    }

    function renderList(items) {
      list.innerHTML = "";
      items.forEach((qa) => {
        const item = document.createElement("div");
        item.className = "mlx-chat-item";
        item.setAttribute("role", "listitem");
        item.innerHTML = `
          <button type="button" class="mlx-chat-q">${escHtml(qa.question)}</button>
          <div class="mlx-chat-a" hidden>${qa.answer}</div>
        `;
        const qBtn = item.querySelector(".mlx-chat-q");
        const aDiv = item.querySelector(".mlx-chat-a");
        qBtn.addEventListener("click", () => {
          const hidden = aDiv.hasAttribute("hidden");
          if (hidden) aDiv.removeAttribute("hidden");
          else aDiv.setAttribute("hidden", "");
        });
        list.appendChild(item);
      });
    }

    function filterList(term) {
      const t = (term || "").trim().toLowerCase();
      if (!t) return data.qas;

      return data.qas.filter((qa) => {
        const q = (qa.question || "").toLowerCase();
        // answer is HTML, so we do a cheap strip:
        const a = (qa.answer || "").replace(/<[^>]*>/g, " ").toLowerCase();
        return q.indexOf(t) >= 0 || a.indexOf(t) >= 0;
      });
    }

    // initial render
    renderList(data.qas || []);

    // online/offline behavior
    if (!online) {
      offline.hidden = false;
      contact.classList.add("is-disabled");
      contact.setAttribute("aria-disabled", "true");
      contact.setAttribute("tabindex", "-1");
      contact.addEventListener("click", (e) => e.preventDefault());
    } else {
      const url = buildContactUrl();
      contact.href = url || "#";
      if (!url) {
        contact.classList.add("is-disabled");
        contact.setAttribute("aria-disabled", "true");
        contact.setAttribute("tabindex", "-1");
        contact.addEventListener("click", (e) => e.preventDefault());
      }
    }

    launcher.addEventListener("click", () => setOpen(!isOpen));
    closeBtn.addEventListener("click", () => setOpen(false));

    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && isOpen) setOpen(false);
    });

    search.addEventListener("input", () => {
      renderList(filterList(search.value));
    });

    // click outside closes
    document.addEventListener("click", (e) => {
      if (!isOpen) return;
      const inside = root.contains(e.target);
      if (!inside) setOpen(false);
    });
  }

  if (window.MLXChatBox) {
    createUI(window.MLXChatBox);
  }
})();
