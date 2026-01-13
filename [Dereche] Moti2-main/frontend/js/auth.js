const API = "https://moti2.onrender.com/api";

/* ===== LOGIN ===== */
function login() {
  const username = document.getElementById("username").value;
  const password = document.getElementById("password").value;

  fetch(`${API}/login`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ username, password })
  })
  .then(res => res.json())
  .then(data => {
    if (data.userId) {
      localStorage.setItem("userId", data.userId);
      localStorage.setItem("username", data.username);
      location.href = "dashboard.html";
    } else {
      alert("Login failed");
    }
  });
}

/* ===== REGISTER ===== */
function register() {
  const username = document.getElementById("regUsername").value;
  const password = document.getElementById("regPassword").value;

  fetch(`${API}/register`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ username, password })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      alert("Registered successfully");
      location.href = "index.html";
    } else {
      alert(data.error || "Register failed");
    }
  });
}

/* ===== SESSION GUARD ===== */
function requireLogin() {
  if (!localStorage.getItem("userId")) {
    location.href = "index.html";
  }
}

function logout() {
  localStorage.clear();
  location.href = "index.html";
}
