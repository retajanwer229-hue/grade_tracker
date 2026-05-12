// =====================
// State
// =====================
let allGrades = [];
let sortCol = null;
let sortAsc = true;

// =====================
// Helpers
// =====================
function getPercent(grade) {
  return grade.total > 0 ? (grade.mark / grade.total) * 100 : 0;
}

function getLetterGrade(percent) {
  if (percent >= 90) return 'A+';
  if (percent >= 85) return 'A';
  if (percent >= 80) return 'B+';
  if (percent >= 75) return 'B';
  if (percent >= 70) return 'C+';
  if (percent >= 65) return 'C';
  if (percent >= 60) return 'D';
  return 'F';
}

function isPassing(percent) {
  return percent >= 50;
}

// =====================
// Load Grades (Fetch API)
// =====================
async function loadGrades() {
  try {
    const res = await fetch("backend/getGrades.php");
    if (!res.ok) throw new Error(`Server error: ${res.status}`);
    allGrades = await res.json();

    renderTable(allGrades);
    populateFilter(allGrades);
  } catch (err) {
    console.error("Failed to load grades:", err);
    document.getElementById("gradesBody").innerHTML =
      `<tr><td colspan="6" style="color:var(--fail)">Failed to load grades. Please try again.</td></tr>`;
  }
}

// =====================
// Render Grade Table (DOM manipulation)
// =====================
function renderTable(grades) {
  const tbody = document.getElementById("gradesBody");

  if (grades.length === 0) {
    tbody.innerHTML = `<tr><td colspan="6" style="color:var(--muted)">No grades found.</td></tr>`;
    return;
  }

  // ✅ Array methods: compute percent for display
  tbody.innerHTML = grades.map(g => {
    const percent = getPercent(g);
    const passing  = isPassing(percent);
    const cssClass = passing ? 'grade-pass' : 'grade-fail';

    return `
      <tr>
        <td>${escapeHtml(g.subject)}</td>
        <td>${escapeHtml(g.assessment)}</td>
        <td>${g.mark}</td>
        <td>${g.total}</td>
        <td class="${cssClass}">${percent.toFixed(1)}%</td>
        <td>
          <button class="edit-btn" onclick="openEditModal(${g.id})">Edit</button>
          <button class="delete-btn" onclick="deleteGrade(${g.id})">Delete</button>
        </td>
      </tr>
    `;
  }).join('');
}

// =====================
// Populate Subject Filter
// =====================
function populateFilter(grades) {
  const select = document.getElementById("subjectFilter");
  // ✅ Array.map + Set to get unique subjects
  const subjects = [...new Set(grades.map(g => g.subject))].sort();
  select.innerHTML = `<option value="all">All Subjects</option>` +
    subjects.map(s => `<option value="${s}">${escapeHtml(s)}</option>`).join('');
}

// =====================
// Sort Functionality (Array.sort)
// =====================
function setupSort() {
  document.querySelectorAll("th.sortable").forEach(th => {
    th.addEventListener("click", () => {
      const col = th.dataset.col;
      if (sortCol === col) {
        sortAsc = !sortAsc;
      } else {
        sortCol = col;
        sortAsc = true;
      }

      const sorted = [...allGrades].sort((a, b) => {
        let valA, valB;
        if (col === 'percent') {
          valA = getPercent(a);
          valB = getPercent(b);
        } else {
          valA = typeof a[col] === 'string' ? a[col].toLowerCase() : a[col];
          valB = typeof b[col] === 'string' ? b[col].toLowerCase() : b[col];
        }
        if (valA < valB) return sortAsc ? -1 : 1;
        if (valA > valB) return sortAsc ? 1 : -1;
        return 0;
      });

      renderTable(sorted);
    });
  });
}

// =====================
// Add Grade Form (Fetch, no page reload)
// =====================
function setupAddGradeForm() {
  const form = document.getElementById("addGradeForm");
  const msg  = document.getElementById("formMessage");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    msg.textContent = '';

    const formData = new FormData(form);

    try {
      const res = await fetch("backend/addGrade.php", {
        method: "POST",
        body: formData
      });
      const data = await res.json();

      if (data.success) {
        msg.style.color = 'var(--pass)';
        msg.textContent = '✅ Grade added!';
        form.reset();
        await loadGrades();       // ✅ Refresh table after adding
        await loadSummary();      // ✅ Refresh summary cards & chart
      } else {
        msg.style.color = 'var(--fail)';
        msg.textContent = '❌ ' + (data.error || 'Unknown error');
      }
    } catch (err) {
      msg.style.color = 'var(--fail)';
      msg.textContent = '❌ Network error. Please try again.';
    }
  });
}

// =====================
// Delete Grade
// =====================
async function deleteGrade(id) {
  if (!confirm("Delete this grade?")) return;

  try {
    const formData = new FormData();
    formData.append("id", id);

    const res = await fetch("backend/deleteGrade.php", {
      method: "POST",
      body: formData
    });
    const data = await res.json();

    if (data.success) {
      await loadGrades();
      await loadSummary();
    } else {
      alert("❌ " + (data.error || "Could not delete grade."));
    }
  } catch (err) {
    alert("❌ Network error.");
  }
}

// =====================
// Edit Grade Modal
// =====================
function openEditModal(id) {
  const grade = allGrades.find(g => g.id === id);
  if (!grade) return;

  document.getElementById("editId").value         = grade.id;
  document.getElementById("editSubject").value    = grade.subject;
  document.getElementById("editAssessment").value = grade.assessment;
  document.getElementById("editMark").value       = grade.mark;
  document.getElementById("editTotal").value      = grade.total;

  document.getElementById("editModal").showModal();
}

function setupEditForm() {
  document.getElementById("closeModal").addEventListener("click", () => {
    document.getElementById("editModal").close();
  });

  document.getElementById("editForm").addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(e.target);

    try {
      const res = await fetch("backend/editGrade.php", {
        method: "POST",
        body: formData
      });
      const data = await res.json();

      if (data.success) {
        document.getElementById("editModal").close();
        await loadGrades();
        await loadSummary();
      } else {
        alert("❌ " + (data.error || "Could not update grade."));
      }
    } catch (err) {
      alert("❌ Network error.");
    }
  });
}

// =====================
// Subject Filter
// =====================
function setupFilter() {
  document.getElementById("subjectFilter").addEventListener("change", (e) => {
    const val = e.target.value;
    const filtered = val === 'all' ? allGrades : allGrades.filter(g => g.subject === val);
    renderTable(filtered);
  });
}

// =====================
// Summary: Cards + Chart + GPA
// =====================
let chartInstance = null;

async function loadSummary() {
  try {
    const res = await fetch("backend/subjectSummary.php");
    if (!res.ok) throw new Error("Server error");
    const data = await res.json();

    renderSummaryCards(data);
    renderChart(data);
    renderGPA(data);
  } catch (err) {
    console.error("Failed to load summary:", err);
  }
}

function renderSummaryCards(data) {
  const container = document.getElementById("summaryCards");

  container.innerHTML = data.map(d => {
    const letter  = getLetterGrade(d.avg_percent);
    const passing = isPassing(d.avg_percent);
    const cls     = passing ? 'grade-pass' : 'grade-fail';

    return `
      <div class="card">
        <h3>${escapeHtml(d.subject)}</h3>
        <span class="card-avg">${d.avg_percent.toFixed(1)}%</span>
        <span class="card-letter ${cls}">${letter}</span>
      </div>
    `;
  }).join('');
}

function renderGPA(data) {
  const gpaEl = document.getElementById("gpaDisplay");
  if (data.length === 0) {
    gpaEl.textContent = "Overall Average: —";
    return;
  }
  // ✅ Average calculation using Array methods (reduce)
  const overall = data.reduce((sum, d) => sum + d.avg_percent, 0) / data.length;
  const letter  = getLetterGrade(overall);
  gpaEl.textContent = `Overall Average: ${overall.toFixed(1)}% (${letter})`;
}

function renderChart(data) {
  const ctx = document.getElementById("gradesChart").getContext("2d");

  if (chartInstance) chartInstance.destroy();

  chartInstance = new Chart(ctx, {
    type: "bar",
    data: {
      labels: data.map(d => d.subject),
      datasets: [{
        label: "Average %",
        data: data.map(d => d.avg_percent),
        backgroundColor: data.map(d => isPassing(d.avg_percent) ? '#4caf50' : '#d43f3f')
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          max: 100,
          ticks: { color: '#e0e0e0' },
          grid:  { color: '#333' }
        },
        x: {
          ticks: { color: '#e0e0e0' },
          grid:  { color: '#333' }
        }
      },
      plugins: {
        legend: { labels: { color: '#e0e0e0' } }
      }
    }
  });
}

// =====================
// XSS Protection
// =====================
function escapeHtml(str) {
  const div = document.createElement('div');
  div.appendChild(document.createTextNode(str));
  return div.innerHTML;
}

// =====================
// Init
// =====================
document.addEventListener("DOMContentLoaded", async () => {
  await loadGrades();
  await loadSummary();
  setupSort();
  setupAddGradeForm();
  setupEditForm();
  setupFilter();
});
