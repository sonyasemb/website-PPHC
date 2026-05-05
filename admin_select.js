(() => {
  const selectors = "select";

  function closeAll(except) {
    document.querySelectorAll(".pphc-select.is-open").forEach((el) => {
      if (el !== except) {
        el.classList.remove("is-open");
      }
    });
  }

  function updateLabel(select, label, options) {
    const selected = select.options[select.selectedIndex];
    label.textContent = selected ? selected.text : "Pilih";
    options.querySelectorAll(".pphc-select-option").forEach((opt) => {
      opt.classList.toggle("is-active", opt.dataset.value === select.value);
    });
  }

  function buildOptions(select, options) {
    options.innerHTML = "";
    Array.from(select.options).forEach((opt) => {
      const item = document.createElement("div");
      item.className = "pphc-select-option";
      item.dataset.value = opt.value;
      item.textContent = opt.text;
      if (opt.disabled) {
        item.classList.add("is-disabled");
      }
      if (opt.selected) {
        item.classList.add("is-active");
      }
      options.appendChild(item);
    });
  }

  function enhanceSelect(select) {
    if (select.dataset.pphcEnhanced === "1") return;
    if (select.multiple) return;
    select.dataset.pphcEnhanced = "1";

    const wrapper = document.createElement("div");
    wrapper.className = "pphc-select";
    if (select.disabled) wrapper.classList.add("is-disabled");

    const parent = select.parentNode;
    parent.insertBefore(wrapper, select);
    wrapper.appendChild(select);

    select.classList.add("pphc-select-native");

    const trigger = document.createElement("button");
    trigger.type = "button";
    trigger.className = "pphc-select-trigger";
    trigger.disabled = select.disabled;

    const label = document.createElement("span");
    label.className = "pphc-select-label";

    const caret = document.createElement("span");
    caret.className = "pphc-select-caret";

    trigger.appendChild(label);
    trigger.appendChild(caret);
    wrapper.appendChild(trigger);

    const options = document.createElement("div");
    options.className = "pphc-select-options";
    buildOptions(select, options);
    wrapper.appendChild(options);

    updateLabel(select, label, options);

    trigger.addEventListener("click", (event) => {
      event.stopPropagation();
      if (select.disabled) return;
      const isOpen = wrapper.classList.contains("is-open");
      closeAll(wrapper);
      wrapper.classList.toggle("is-open", !isOpen);
    });

    options.addEventListener("click", (event) => {
      const item = event.target.closest(".pphc-select-option");
      if (!item || item.classList.contains("is-disabled")) return;
      select.value = item.dataset.value;
      updateLabel(select, label, options);
      select.dispatchEvent(new Event("change", { bubbles: true }));
      wrapper.classList.remove("is-open");
    });

    select.addEventListener("change", () => {
      buildOptions(select, options);
      updateLabel(select, label, options);
    });
  }

  document.addEventListener("click", () => closeAll(null));
  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      closeAll(null);
    }
  });

  document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(selectors).forEach(enhanceSelect);
  });
})();
