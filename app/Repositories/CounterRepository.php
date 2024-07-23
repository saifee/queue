<?php

namespace App\Repositories;

use App\Models\Counter;


class CounterRepository
{

    public function getAllActiveCounters()
    {
        return Counter::where('status', true)->get();
    }

    public function getCounterById($id)
    {
        return Counter::find($id);
    }

    public function create($data)
    {
        $counter = Counter::create([
            'name' => $data['name'],
            'status' => 1
        ]);
        return $counter;
    }
    public function update($data, $counter)
    {
        $counter->name = $data['name'];
        $counter->save();
        return $counter;
    }
    public function delete($data, $counter)
    {
        $counter->delete();
    }
}
