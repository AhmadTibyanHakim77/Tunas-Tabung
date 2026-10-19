<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Student> */
class StudentFactory extends Factory
{
    public const PROFILES = [
        ['name' => 'Ahmad Fadillah', 'gender' => 'Laki-laki'],
        ['name' => 'Aisyah Putri', 'gender' => 'Perempuan'],
        ['name' => 'Bima Pratama', 'gender' => 'Laki-laki'],
        ['name' => 'Annisa Rahma', 'gender' => 'Perempuan'],
        ['name' => 'Dimas Saputra', 'gender' => 'Laki-laki'],
        ['name' => 'Citra Lestari', 'gender' => 'Perempuan'],
        ['name' => 'Fajar Ramadhan', 'gender' => 'Laki-laki'],
        ['name' => 'Fitria Ningsih', 'gender' => 'Perempuan'],
        ['name' => 'Galang Maulana', 'gender' => 'Laki-laki'],
        ['name' => 'Intan Permata', 'gender' => 'Perempuan'],
        ['name' => 'Ilham Akbar', 'gender' => 'Laki-laki'],
        ['name' => 'Nabila Safitri', 'gender' => 'Perempuan'],
        ['name' => 'Reza Firmansyah', 'gender' => 'Laki-laki'],
        ['name' => 'Putri Amelia', 'gender' => 'Perempuan'],
        ['name' => 'Rizky Kurniawan', 'gender' => 'Laki-laki'],
        ['name' => 'Salsabila Aulia', 'gender' => 'Perempuan'],
        ['name' => 'Bagas Aditya', 'gender' => 'Laki-laki'],
        ['name' => 'Zahra Khairunnisa', 'gender' => 'Perempuan'],
        ['name' => 'Arif Nugroho', 'gender' => 'Laki-laki'],
        ['name' => 'Dinda Maharani', 'gender' => 'Perempuan'],
        ['name' => 'Rafi Alfarizi', 'gender' => 'Laki-laki'],
        ['name' => 'Aulia Rahmawati', 'gender' => 'Perempuan'],
        ['name' => 'Fikri Ramadhan', 'gender' => 'Laki-laki'],
        ['name' => 'Kirana Putri', 'gender' => 'Perempuan'],
    ];

    public function definition(): array
    {
        $profile = fake()->randomElement(self::PROFILES);

        return [
            'student_number' => fake()->unique()->numerify('2026###'),
            'name' => $profile['name'],
            'class' => fake()->randomElement(['1A', '2A', '3A', '4A', '5A', '6A']),
            'gender' => $profile['gender'],
            'date_of_birth' => fake()->dateTimeBetween('-12 years', '-6 years')->format('Y-m-d'),
            'is_active' => true,
        ];
    }
}
