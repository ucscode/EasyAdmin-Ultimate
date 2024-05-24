<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\ParameterBag;
use ZxcvbnPhp\Zxcvbn;

/**
 * Estimates the strength of a password based on the Zxcvbn algorithm.
 *
 * This class uses the zxcvbn-php library to provide a realistic password strength estimate.
 * It is based on the original Zxcvbn JavaScript project from Dropbox.
 *
 * @see https://github.com/bjeavons/zxcvbn-php
 */
class PasswordStrengthEstimator
{
    protected Zxcvbn $estimator;

    /**
     * Constructor that initializes the Zxcvbn password strength estimator.
     */
    public function __construct()
    {
        $this->estimator = new Zxcvbn();
    }

    /**
     * Retrieves the underlying Zxcvbn password strength estimator.
     *
     * @return Zxcvbn The Zxcvbn password strength estimator instance.
     */
    public function getEstimator(): Zxcvbn
    {
        return $this->estimator;
    }

    /**
     * Estimates the strength of a password based on the Zxcvbn algorithm.
     *
     * @param string $password The password to be evaluated.
     * @param array $userInputs Additional user-specific inputs to consider when evaluating the password strength.
     * @return ParameterBag The password strength evaluation results, including the score and feedback.
     */
    public function getPasswordStrength(string $password, array $userInputs = []): ParameterBag
    {
        return self::traverseParameters($this->estimator->passwordStrength($password, $userInputs));
    }

    /**
     * Recursively convert all array values to ParameterBag Instances
     * 
     * @param array $parameters The parameters to convert into parameter bag
     * @return ParameterBag The resultant parameter bag
     */
    private static function traverseParameters(array $parameters): ParameterBag
    {
        foreach($parameters as &$parameter) {
            if(is_array($parameter)) {
                $parameter = self::traverseParameters($parameter);
            }
        }
        return new ParameterBag($parameters);
    }
}