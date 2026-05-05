const months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
const now = new Date();
let currentMonth = now.getMonth(), currentYear = now.getFullYear();
const calTitle = document.getElementById("calendar-title");
const calendarBody = document.getElementById("calendar-body");
const summaryBox = document.getElementById("summary-box");
const summaryText = document.getElementById("summary-text");
const layanan = (window.USER_BOOKING_CONFIG && window.USER_BOOKING_CONFIG.layanan) || "";
const slots = document.querySelectorAll(".time-slot");
let selectedDate = null, selectedTime = null;

function updateSummary() {
  if (selectedDate && selectedTime) {
    summaryText.textContent = "Anda memilih: " + selectedDate + " jam " + selectedTime + " WITA";
    summaryBox.style.display = "block";
  }
}

function resetSelection() {
  selectedDate = null;
  selectedTime = null;
  slots.forEach((s) => s.classList.remove("active"));
  summaryBox.style.display = "none";
}

function renderCalendar() {
  calTitle.textContent = months[currentMonth] + " " + currentYear;
  calendarBody.innerHTML = "";
  const firstDay = new Date(currentYear, currentMonth, 1).getDay();
  const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
  const today = new Date();
  const minDate = new Date(today.getFullYear(), today.getMonth(), today.getDate());
  let date = 1;

  for (let i = 0; i < 6; i++) {
    const row = document.createElement("tr");
    for (let j = 0; j < 7; j++) {
      const cell = document.createElement("td");
      if ((i === 0 && j < firstDay) || date > daysInMonth) {
        cell.textContent = "";
      } else {
        cell.textContent = date;
        const cellDate = new Date(currentYear, currentMonth, date);
        if (cellDate < minDate) {
          cell.style.color = "#ccc";
          cell.style.cursor = "not-allowed";
        } else {
          const day = date;
          cell.onclick = function () {
            document.querySelectorAll("td").forEach((td) => td.classList.remove("active-date"));
            cell.classList.add("active-date");
            selectedDate = day + " " + months[currentMonth] + " " + currentYear;
            updateSummary();
          };
        }
        date++;
      }
      row.appendChild(cell);
    }
    calendarBody.appendChild(row);
    if (date > daysInMonth) break;
  }
}

document.getElementById("prev-month").onclick = function () {
  currentMonth = currentMonth === 0 ? 11 : currentMonth - 1;
  if (currentMonth === 11) currentYear--;
  resetSelection();
  renderCalendar();
};

document.getElementById("next-month").onclick = function () {
  currentMonth = currentMonth === 11 ? 0 : currentMonth + 1;
  if (currentMonth === 0) currentYear++;
  resetSelection();
  renderCalendar();
};

slots.forEach((slot) => {
  slot.onclick = function () {
    slots.forEach((s) => s.classList.remove("active"));
    slot.classList.add("active");
    selectedTime = slot.textContent;
    updateSummary();
  };
});

document.getElementById("lanjutBtn").onclick = function () {
  if (!selectedDate || !selectedTime) return alert("Pilih tanggal dan jam terlebih dahulu.");
  const tipeSelect = document.getElementById("tipeKonseling");
  const selectedType = tipeSelect.options[tipeSelect.selectedIndex].text.trim();
  window.location.href = "form.php?date=" + encodeURIComponent(selectedDate) +
    "&time=" + encodeURIComponent(selectedTime) +
    "&tipe=" + encodeURIComponent(selectedType) +
    "&layanan=" + encodeURIComponent(layanan);
};

renderCalendar();
