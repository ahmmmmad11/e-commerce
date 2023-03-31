<?php

namespace App\Filters;

class Filter
{
    protected object|array|null $data = [];

    public function filter(): self
    {
        return $this;
    }

    public function paginate($rows = 30)
    {
        return $this->data->paginate($rows);
    }

    public function get()
    {
        $this->filter();

        if ($per_page = request('paginate')) {
            return $this->paginate($per_page);
        }

        return $this->data->get();
    }
}
