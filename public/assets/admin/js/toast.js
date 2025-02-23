window.ToastsMessage = function ({ title = "", message = "", type = "info", alertClass = "toast--info", duration = 3000 }) {
  const icons = {
    success: "fas fa-check-circle",
    info: "fas fa-info-circle",
    warning: "fas fa-exclamation-circle",
    error: "fas fa-exclamation-circle"
  };
  const icon = icons[type];
  const delay = (duration / 1000).toFixed(2);

  var toast = document.createElement('div');
  toast.classList.add("toast", `toast--${type}`, "slideInLeft", "fadeOut");
  toast.style.animationDelay = `${delay}s`;

  var alertContainer = document.getElementById('alertContainer');
  if (!alertContainer) {
    alertContainer = document.createElement('div');
    alertContainer.id = 'alertContainer';
    alertContainer.style.position = 'fixed';
    alertContainer.style.bottom = '10px';
    alertContainer.style.right = '10px';
    alertContainer.style.zIndex = '9999';
    alertContainer.style.maxHeight = 'calc(100vh - 20px)';
    alertContainer.style.overflowY = 'auto';
    document.body.appendChild(alertContainer);
  }

  var alert = document.createElement('div');
  alert.className = `toast ${alertClass} fade d-flex align-items-center`;
  alert.role = 'alert';
  alert.setAttribute('aria-live', 'assertive');
  alert.setAttribute('aria-atomic', 'true');
  alert.innerHTML = `
      <div class="toast__icon mr-2">
          <i class="${icon}"></i>
      </div>
      <div class="toast__body flex-grow-1">
          <h3 class="toast__title">${title}</h3>
          <p class="toast__msg mb-0">${message}</p>
      </div>
      <div class="toast__close ml-2">
          <i class="fas fa-times"></i>
      </div>
  `;

  alertContainer.appendChild(alert);

  // Limit to 4 toasts
  if (alertContainer.children.length > 3) {
    alertContainer.removeChild(alertContainer.firstChild);
  }

  $(alert).toast({ delay: duration });
  $(alert).toast('show');

  setTimeout(() => {
    $(alert).toast('dispose');
    alert.remove();
  }, duration + 500);
};
