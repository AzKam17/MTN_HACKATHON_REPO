import React from 'react'
import axios from 'axios'
import { useForm } from 'react-hook-form'
import { Routes, Route, Link } from 'react-router-dom'


function Login() {
  const {
    register,
    handleSubmit,
    watch,
    formState: { errors },
  } = useForm()
  const onSubmit = (data) => {
    axios
      .post('http://localhost:55000/api/login_check', data)
      .then((res) => {
        console.log(res)
      })
      .catch((err) => {
        console.log(err)
      })
  }

  //Regex pattern for 10 digit phone number
  const phonePattern = /^\d{10}$/

  return (
    <div
      className="h-screen flex flex-col items-center justify-center px-5 bg-opacity-50 bg-blue-300 relative z-30"
    >
      <h1 className="font-extrabold text-2xl">Bienvenue !</h1>
      <p className="text-center">
        Connectez-vous pour accéder à votre espace personnel
      </p>
      <hr className="w-full my-2 bg-black h-0.5 border-black" />
      <form
        onSubmit={handleSubmit(onSubmit)}
        className="flex flex-col items-center justify-center w-full"
      >
        <input
          className="input-primary my-2"
          type="number"
          placeholder="Numéro de téléphone"
          {...register('phone', {
            required: true,
            pattern: phonePattern,
            minLength: 10,
            maxLength: 10,
          })}
        />
        {errors.email && (
          <span className="text-red-500">Ce champ est requis</span>
        )}
        <input
          className="input-primary my-2 mt-0"
          type="password"
          placeholder="Mot de passe"
          {...register('password', { required: true })}
        />
        {errors.password && (
          <span className="text-red-500">Ce champ est requis</span>
        )}
        <button className="btn-primary w-full" type="submit">
          Connexion
        </button>
      </form>
      <hr className="w-full my-2 bg-black h-0.5 border-black" />
      <Link to="/register" className=" w-full">
        <button className="btn-primary bg-indigo-500 w-full mt-1">
          Pas encore inscrit ?
        </button>
      </Link>
    </div>
  )
}

export default Login
