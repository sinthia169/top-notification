function showNotification(message, duration) {
  const notif = document.getElementById('top-notification');
  if (!notif) return;
  notif.style.display = 'block';
  setTimeout(() => {
      notif.style.display = 'none';
  }, duration);
}

function hideNotification() {
  const notif = document.getElementById('top-notification');
  if (notif) notif.style.display = 'none';
}

window.addEventListener('DOMContentLoaded', function () {
  const notif = document.getElementById('top-notification');
  if (notif) {
      showNotification(notif.innerText, 4000);
  }
});
