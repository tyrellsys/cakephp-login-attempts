<?php

namespace LoginAttempts\Authenticator;

use Authentication\Authenticator\FormAuthenticator as BaseFormAuthenticator;
use Authentication\Authenticator\Result;
use Authentication\Authenticator\ResultInterface;
use Authentication\Identifier\IdentifierInterface;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use LoginAttempts\Model\Table\AttemptsTableInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * LoginAttempts Form Authenticator class
 */
class FormAuthenticator extends BaseFormAuthenticator
{

    /**
     * construct
     *
     * @param IdentifierInterface $identifier Identifier or identifiers collection.
     * @param array $config Array of config to use.
     */
    public function __construct(IdentifierInterface $identifier, array $config = [])
    {
        $this->_defaultConfig += [
            'userModel' => 'Users',
            'attemptLimit' => 5,
            'attemptDuration' => '+5 minutes',
            'attemptAction' => 'login',
            'attemptsStorageModel' => 'LoginAttempts.Attempts',
        ];
        parent::__construct($identifier, $config);
    }

    /**
     * get action name
     *
     * @return string
     */
    protected function _getAction()
    {
        return $this->getConfig('userModel') . '.' . $this->getConfig('attemptAction');
    }

    /**
     * authenticate & check attempt counts
     *
     * @param ServerRequest $request The request that contains login information.
     * @param ResponseInterface $response Unused response object.
     * @return \Authentication\Authenticator\ResultInterface
     */
    public function authenticate(ServerRequestInterface $request, ResponseInterface $response)
    {
        $ip = $request->clientIp();
        $action = $this->_getAction();
        $attempts = $this->getAttemptsTable();

        // check attempts
        if (!$attempts->check($ip, $action, $this->getConfig('attemptLimit'))) {
            return new Result(null, ResultInterface::FAILURE_OTHER);
        }

        $result = parent::authenticate($request, $response);
        if ($result->isValid()) {
            // on success clear attempts
            $attempts->reset($ip, $action);
        } elseif ($result->getStatus() === ResultInterface::FAILURE_IDENTITY_NOT_FOUND) {
            // on failure record attempts
            $attempts->fail($ip, $action, $this->getConfig('attemptDuration'));
        }

        return $result;
    }

    /**
     * @return AttemptsTableInterface
     */
    protected function getAttemptsTable()
    {
        return TableRegistry::getTableLocator()->get($this->getConfig('attemptsStorageModel'));
    }
}
