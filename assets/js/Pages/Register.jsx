import React from 'react'
import { useForm } from 'react-hook-form'
import { Routes, Route, Link } from 'react-router-dom'

function Register() {
  const {
    register,
    handleSubmit,
    watch,
    formState: { errors },
  } = useForm()
  const onSubmit = (data) => console.log(data)

  //Regex pattern for 10 digit phone number
  const phonePattern = /^\d{10}$/

  return (
    <div className="h-screen flex flex-col items-center justify-center px-5 bg-blue-400">
      <h1 className="font-bold text-2xl">Inscription</h1>
      <p className="text-center">
        Inscrivez-vous pour accéder à votre espace personnel
      </p>
      <hr className="w-full my-2 bg-black h-0.5" />
      <form
        onSubmit={handleSubmit(onSubmit)}
        className="flex flex-col items-center justify-center w-full"
      >
        <input
          className="input-primary my-2"
          type="text"
          placeholder="Nom"
          {...register('nom', { required: true })}
        />
        {errors.nom && (
          <span className="text-red-500">Ce champ est requis</span>
        )}
        <input
          className="input-primary my-2 mt-0"
          type="text"
          placeholder="Prénoms"
          {...register('prenom', { required: true })}
        />
        {errors.prenom && (
          <span className="text-red-500">Ce champ est requis</span>
        )}
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
        {errors.phone && (
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
          Inscription
        </button>
      </form>
      <Link to="/login" className=" w-full">
        <button className="btn-primary bg-indigo-500 w-full mt-1">
          J&apos;ai déjà un compte
        </button>
      </Link>
    </div>
  )
}

export default Register
