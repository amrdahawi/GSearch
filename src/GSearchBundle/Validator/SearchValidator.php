<?php

namespace GSearchBundle\Validator;

use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Validation;

class SearchValidator extends Validator
{
    /**
     * @param array $keywords
     * @param array $urls
     * @return bool
     */
    public function isValid(array $keywords, array $urls)
    {
        // check that the number of keywords and urls are the same
        if (count($keywords) != count($urls)) {
            $this->addError('number of keywords does not match number of urls');
        }
        $invalidUrls = [];
        $validator = Validation::createValidator();
        // validate urls
        foreach ($urls as $url) {
            $violations = $validator->validate($url, new Url());
            if (count($violations) !== 0) {
                $invalidUrls[] = $url;
            }
        }
        // if there are any invalid urls, added them to the error message
        if ($invalidUrls) {
            $this->addError('Invalid url(s): ' . implode(',', $invalidUrls));
        }

        return $this->hasErrors();
    }

}
