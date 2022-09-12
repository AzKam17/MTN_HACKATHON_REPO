import React from 'react'
import { Routes, Route, Link } from 'react-router-dom'
import { motion } from "framer-motion"


import Login from './Pages/Login'
import Register from './Pages/Register'
import HomePage from './Pages/Auth/HomePage'

function App() {
  return (
    <div className="App">
      <Routes>
        <Route path="auth/homepage" element={<HomePage />} />
        <Route path="register" element={<Register />} />
        <Route path="login" element={<Login />} />
      </Routes>
    </div>
  )
}

export default App
