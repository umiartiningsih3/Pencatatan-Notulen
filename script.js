function tambahAgenda() {
  const ol = document.getElementById('noteList');
  const li = document.createElement('li');
  li.contentEditable = "true";
  li.textContent = "Tulis catatan baru di sini...";
  ol.appendChild(li);
  li.focus();
}