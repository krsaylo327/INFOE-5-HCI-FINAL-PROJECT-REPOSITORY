const API = "https://moti2.onrender.com/api";
const userId = localStorage.getItem("userId");
if(!userId) location.href="index.html";

/* ===== NOTES ===== */
function addNote(){
  if(!noteInput.value) return;
  fetch(`${API}/notes`,{
    method:"POST",
    headers:{"Content-Type":"application/json"},
    body:JSON.stringify({userId,content:noteInput.value})
  }).then(()=>{
    noteInput.value="";
    loadNotes();
    loadStats();
  });
}
function loadNotes(){
  fetch(`${API}/notes/${userId}`)
    .then(r=>r.json())
    .then(d=>{
      notesList.innerHTML="";
      d.forEach(n=>notesList.innerHTML+=`<li>${n.content}</li>`);
    });
}

/* ===== HABITS ===== */
function addHabit(){
  if(!habitInput.value) return;
  fetch(`${API}/habits`,{
    method:"POST",
    headers:{"Content-Type":"application/json"},
    body:JSON.stringify({userId,name:habitInput.value})
  }).then(()=>{
    habitInput.value="";
    loadHabits();
    loadStats();
  });
}
function loadHabits(){
  fetch(`${API}/habits/${userId}`)
    .then(r=>r.json())
    .then(d=>{
      habitsList.innerHTML="";
      d.forEach(h=>habitsList.innerHTML+=`<li>${h.name}</li>`);
    });
}

/* ===== REMINDERS ===== */
function addReminder(){
  if(!reminderText.value || !reminderTime.value) return;
  fetch(`${API}/reminders`,{
    method:"POST",
    headers:{"Content-Type":"application/json"},
    body:JSON.stringify({
      userId,
      text:reminderText.value,
      remind_at:reminderTime.value
    })
  }).then(()=>{
    reminderText.value="";
    reminderTime.value="";
    loadReminders();
    loadStats();
  });
}
function loadReminders(){
  fetch(`${API}/reminders/${userId}`)
    .then(r=>r.json())
    .then(d=>{
      remindersList.innerHTML="";
      d.forEach(r=>remindersList.innerHTML+=
        `<li>${r.text} (${r.remind_at})</li>`);
    });
}

/* ===== ANALYTICS ===== */
function loadStats(){
  fetch(`${API}/stats/${userId}`)
    .then(r=>r.json())
    .then(s=>{
      analytics.innerHTML = `
        Notes: <b>${s.notes}</b><br>
        Habits: <b>${s.habits}</b><br>
        Reminders: <b>${s.reminders}</b>
      `;
    });
}

/* INIT */
loadNotes();
loadHabits();
loadReminders();
loadStats();
