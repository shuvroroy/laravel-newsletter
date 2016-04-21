<?php

namespace Spatie\Newsletter;

use Illuminate\Support\Collection;
use Spatie\Newsletter\Exceptions\InvalidNewsletterList;

class NewsletterListCollection extends Collection
{
    /** @var string */
    public $defaultListName = '';

    /**
     * @param array $config
     *
     * @return static
     */
    public static function makeForConfig($config)
    {
        $collection = new static();

        foreach ($config['lists'] as $name => $listProperties) {
            $collection->push(new NewsletterList($name, $listProperties));
        }

        $collection->defaultListName = $config['defaultListName'];

        return $collection;
    }

    /**
     * @param string $name
     *
     * @return \Spatie\Newsletter\NewsletterList
     * 
     * @throws \Spatie\Newsletter\Exceptions\InvalidNewsletterList
     */
    public function findByName($name)
    {
        if ($name == '') {
            return $this->getDefault();
        }

        $list = $this->first(function ($index, NewsletterList $newletterList) use ($name) {
            return $newletterList->getName() === $name;
        });

        if (is_null($list)) {
            throw InvalidNewsletterList::noListWithName($name);
        }

        return $list;
    }

    /**
     * @return \Spatie\Newsletter\NewsletterList
     *
     * @throws \Spatie\Newsletter\Exceptions\InvalidNewsletterList
     */
    public function getDefault()
    {
        $defaultList = $this->first(function ($index, NewsletterList $newletterList) {
            return $newletterList->getName() === $this->defaultListName;
        });

        if (is_null($defaultList)) {
            throw InvalidNewsletterList::defaultListDoesNotExist($this->defaultListName);
        }

        return $defaultList;
    }
}
