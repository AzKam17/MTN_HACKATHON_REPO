import React from 'react'

import login from '../../../img/login.jpeg'

//Create a tailwind styled component with two buttons fixed at the bottom of the page
const HomePage = () => {
  return (
    <div className="flex flex-col items-center justify-center h-screen px-5">
      <img src={login} alt="login" className="scale-125 z-0" />
      <div className="z-10">
        <div className="flex flex-col items-center justify-center">
          <h1 className="text-5xl font-bold text-gray-800 mt-3">Bienvenue à <br/>E-Tontine</h1>
          <p className="text-gray-600 mt-1">L'entraide pour tous</p>

          <button className="btn-primary w-full mt-5">
            Inscription
          </button>

          <button className="btn-primary w-full mt-2">
            Connexion
          </button>
        </div>
      </div>
    </div>
  )
}

export default HomePage