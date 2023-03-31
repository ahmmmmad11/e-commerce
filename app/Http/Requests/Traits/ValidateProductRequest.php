<?php

namespace App\Http\Requests\Traits;

use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

trait ValidateProductRequest
{
    private array $required_properties = [];

    private Collection $restricted_properties;

    public function getParentsProperties($category): void
    {
        if ($category->parent_id) {
            $this->getParents($category->parent);
        }

        $this->required_properties += $category->properties
            ->where('required', 1)->sortBy('name')->pluck('name')->toArray();


        $this->restricted_properties = $this->restricted_properties
            ->merge($category->properties->where('restricted', 1));
    }

    /**
     * @throws ValidationException
     */
    private function validateCategoryRequiredProperties(): void
    {
        $this->restricted_properties = collect([]);

        $this->getParentsProperties($this->category);

        $request_options = collect($this->options)->sortBy('name')->pluck('name');

        $check = $request_options->intersect($this->required_properties)->toArray() == $this->required_properties;

        if (!$check) {
            $properties = implode(',', $this->required_properties);

            throw ValidationException::withMessages(['options' =>
                __("options should contains the following properties: $properties")]);
        }
    }

    /**
     * @throws ValidationException
     */
    private function validateCategoryRestrictedProperties(): void
    {
        $request_options = collect($this->options)->whereIn('name', $this->restricted_properties->pluck('name'));

        $request_options?->each(function ($item, $key) {
            $options = $this->restricted_properties->where('name', $item['name'])->first()['options'];

            collect($item['options'])->each(function ($option, $key) use ($options, $item) {
                if (!in_array($option['value'], $options)) {
                    $options = implode(',', $options);

                    throw ValidationException::withMessages(["options" =>
                        __("{$item['name']} should only contain one or more of the following values: $options")]);
                }
            });
        });
    }
}
