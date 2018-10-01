<?php

namespace App\Http\Get;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

/**
 * Fetch any GET parameters attached to the end of the URI and validate
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Parameters
{
    private $collection_parameters = [];
    private $request;

    /**
     * Parameters constructor.
     *
     * @param Request $request
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Fetch GET parameters from the URI and check to see if they are valid for
     * the request
     *
     * @param array $parameter_names
     *
     * @return Parameters
     */
    public function fetch(array $parameter_names = []): Parameters
    {
        $request_parameters = $this->request->all();
        $this->collection_parameters = [];

        foreach ($parameter_names as $parameter) {
            if (array_key_exists($parameter, $request_parameters) === true &&
                $request_parameters[$parameter] !== null &&
                $request_parameters[$parameter] !== 'nill') {
                $this->collection_parameters[$parameter] = $request_parameters[$parameter];
            }
        }

        return $this;
    }

    /**
     * Validate the valid parameters array, checking the set value to see if it is
     * valid, invalid values are silently removed from the collections array
     *
     * @return Parameters
     */
    public function validate(): Parameters
    {
        foreach (array_keys($this->collection_parameters) as $key) {
            switch ($key) {
                case 'category':
                    if (array_key_exists($key, $this->collection_parameters) === true) {
                        if ((new Category())->
                            where('id', '=', $this->collection_parameters[$key])->exists() === false) {
                            unset($this->collection_parameters[$key]);
                        }
                    }
                    break;

                case 'month':
                    if (array_key_exists($key, $this->collection_parameters) === true) {
                        if (intval($this->collection_parameters[$key]) < 1 ||
                            $this->collection_parameters[$key] > 12) {

                            unset($this->collection_parameters[$key]);
                        }
                    }
                    break;

                case 'sub_category':
                    if (array_key_exists($key, $this->collection_parameters) === true) {
                        if (
                            (new SubCategory())->
                            where('sub_category.id', '=', $this->collection_parameters[$key])->
                            where('sub_category.category_id', '=', $this->collection_parameters['category'])->
                            exists() === false
                        ) {
                            unset($this->collection_parameters[$key]);
                        }
                    }
                    break;

                case 'year':
                    if (array_key_exists($key, $this->collection_parameters) === true) {
                        if (intval($this->collection_parameters[$key]) < 2013 ||
                            $this->collection_parameters[$key] > intval(date('Y'))) {

                            unset($this->collection_parameters[$key]);
                        }
                    }
                    break;

                default:
                    // Do nothing
                    break;
            }
        }

        return $this;
    }

    /**
     * Return all the valid collection parameters
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->collection_parameters;
    }
}
