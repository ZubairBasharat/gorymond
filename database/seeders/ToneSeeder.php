<?php

use Illuminate\Database\Seeder;
use App\Models\Tone;

class ToneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tones = [
            ['name' => '2tone', 'file' => 'IA2qVmud8u8Ga2aOJZkMIl9UmKcCGiXPI5QsD0kU.mp3'],
            ['name' => 'ping', 'file' => 'IGIUk5bLtFt5gVRmFtc6F5Ll8VSaPdGwiIru06ay.mp3'],
            ['name' => 'arpeggio', 'file' => 'LyVtqYMWnmKKrzEY3StBiOtATFy8wWxL7RW4TWTo.mp3'],
            ['name' => 'knockknock', 'file' => 'g8vyxjgUR03qCDwzmLa4kEnFN9Petln7kh5FTb08.mp3'],
        ];

        foreach ($tones as $tone) {
            Tone::create($tone);
        }
    }
}