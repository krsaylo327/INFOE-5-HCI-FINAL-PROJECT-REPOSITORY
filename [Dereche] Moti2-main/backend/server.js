const express = require("express");
const sqlite3 = require("sqlite3").verbose();
const bcrypt = require("bcryptjs");
const cors = require("cors");

const app = express();
app.use(cors());
app.use(express.json());

const PORT = process.env.PORT || 10000;

// 🔴 Render Persistent Disk
const db = new sqlite3.Database("/data/moti.db");

/* ===== DATABASE TABLES ===== */
db.serialize(() => {
  db.run(`CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE,
    password TEXT
  )`);

  db.run(`CREATE TABLE IF NOT EXISTS notes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    content TEXT
  )`);

  db.run(`CREATE TABLE IF NOT EXISTS habits (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    name TEXT
  )`);

  db.run(`CREATE TABLE IF NOT EXISTS reminders (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    text TEXT,
    remind_at TEXT
  )`);
});

/* ===== AUTH ===== */
app.post("/api/register", async (req,res)=>{
  const { username, password } = req.body;
  const hash = await bcrypt.hash(password,10);
  db.run(
    "INSERT INTO users(username,password) VALUES(?,?)",
    [username,hash],
    err => err
      ? res.status(400).json({error:"User exists"})
      : res.json({success:true})
  );
});

app.post("/api/login",(req,res)=>{
  const { username, password } = req.body;
  db.get(
    "SELECT * FROM users WHERE username=?",
    [username],
    async (_,user)=>{
      if(!user) return res.status(401).json({error:"Invalid"});
      const ok = await bcrypt.compare(password,user.password);
      ok
        ? res.json({userId:user.id,username:user.username})
        : res.status(401).json({error:"Invalid"});
    }
  );
});

/* ===== NOTES ===== */
app.post("/api/notes",(req,res)=>{
  const { userId, content } = req.body;
  db.run(
    "INSERT INTO notes(user_id,content) VALUES(?,?)",
    [userId,content],
    ()=>res.json({success:true})
  );
});

app.get("/api/notes/:uid",(req,res)=>{
  db.all(
    "SELECT * FROM notes WHERE user_id=?",
    [req.params.uid],
    (_,rows)=>res.json(rows)
  );
});

/* ===== HABITS ===== */
app.post("/api/habits",(req,res)=>{
  const { userId, name } = req.body;
  db.run(
    "INSERT INTO habits(user_id,name) VALUES(?,?)",
    [userId,name],
    ()=>res.json({success:true})
  );
});

app.get("/api/habits/:uid",(req,res)=>{
  db.all(
    "SELECT * FROM habits WHERE user_id=?",
    [req.params.uid],
    (_,rows)=>res.json(rows)
  );
});

/* ===== REMINDERS ===== */
app.post("/api/reminders",(req,res)=>{
  const { userId, text, remind_at } = req.body;
  db.run(
    "INSERT INTO reminders(user_id,text,remind_at) VALUES(?,?,?)",
    [userId,text,remind_at],
    ()=>res.json({success:true})
  );
});

app.get("/api/reminders/:uid",(req,res)=>{
  db.all(
    "SELECT * FROM reminders WHERE user_id=? ORDER BY remind_at",
    [req.params.uid],
    (_,rows)=>res.json(rows)
  );
});

/* ===== ANALYTICS ===== */
app.get("/api/stats/:uid",(req,res)=>{
  db.get(
    `
    SELECT
      (SELECT COUNT(*) FROM notes WHERE user_id=?) AS notes,
      (SELECT COUNT(*) FROM habits WHERE user_id=?) AS habits,
      (SELECT COUNT(*) FROM reminders WHERE user_id=?) AS reminders
    `,
    [req.params.uid,req.params.uid,req.params.uid],
    (_,row)=>res.json(row)
  );
});

app.listen(PORT,()=>console.log("✅ MOTI backend running on",PORT));
