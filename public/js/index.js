/* global document */

window.onload = () => {
  const submitFictiveForm = (method, action) => {
    const form = document.createElement('FORM');
    form.name = 'fictiveForm';
    form.method = method;
    form.action = action;
    form.style = { display: 'none' };
    document.body.appendChild(form);
    form.submit();
  }

  const confirmFunction = (e) => {
    e.preventDefault();
    const el = e.currentTarget;
    const confirmText = el.dataset.confirm;
    if (confirm(confirmText)) {
      const method = el.dataset.method;
      const action = el.href;
      if (method && action) submitFictiveForm(method, action);
    }
  }

  const elementsConfirm = document.querySelectorAll('[data-confirm]');
  elementsConfirm.forEach((el) => el.addEventListener('click', confirmFunction));
};
