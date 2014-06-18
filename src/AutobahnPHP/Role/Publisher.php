<?php
/**
 * Created by PhpStorm.
 * User: matt
 * Date: 6/7/14
 * Time: 12:03 PM
 */

namespace AutobahnPHP\Role;


use AutobahnPHP\AbstractSession;
use AutobahnPHP\ClientSession;
use AutobahnPHP\Message\Message;
use AutobahnPHP\Message\PublishMessage;
use AutobahnPHP\Session;
use React\Promise\Deferred;

/**
 * Class Publisher
 * @package AutobahnPHP\Role
 */
class Publisher extends AbstractRole
{
    /**
     * @var ClientSession
     */
    private $session;

    /**
     * @param $session
     */
    function __construct($session)
    {
        $this->session = $session;
    }

    /**
     * @param \AutobahnPHP\AbstractSession $session
     * @param Message $msg
     * @return mixed
     */
    public function onMessage(AbstractSession $session, Message $msg)
    {
        // TODO: Implement onMessage() method.
    }

    /**
     * @param Message $msg
     * @return mixed
     */
    public function handlesMessage(Message $msg)
    {
        $handledMessages = array(
            Message::MSG_PUBLISHED,
        );

        return in_array($msg->getMsgCode(), $handledMessages);
    }

    /**
     * @param $topicName
     * @param $arguments
     * @return \React\Promise\Promise
     */
    public function publish($topicName, $arguments, $argumentsKw, $options)
    {
        $requestId = Session::getUniqueId();

        if (isset($options['acknowledge']) && $options['acknowledge'] === true) {
            $futureResult = new Deferred();
            $this->publishRequests[$requestId] = ['future_result' => $futureResult];
        }


        $publishMsg = new PublishMessage($requestId, $options, $topicName, $arguments, $argumentsKw);

        $this->session->sendMessage($publishMsg);

        return isset($futureResult) ? $futureResult->promise() : false;
    }

} 