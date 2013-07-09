<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

// Import library dependencies
jimport('joomla.event.plugin');

class plgUserEmail_User_On_Deactivation extends JPlugin
{

	function onUserBeforeSave($user, $isNew, $futureData){
		
		//user gets blocked and was not blocked before
		if(!$isNew && ($user['block'] == 0) && ($futureData['block'] == 1)){
			
			$mailer = JFactory::getMailer();
			$config = JFactory::getConfig();
			$sender = array( 	$config->getValue('config.mailfrom'),
								$config->getValue('config.fromname')
							);
			$mailer->setSender($sender);
			$mailer->addRecipient($user['email']);
			
			$subject = $this->params->get('subject');
			$mailer->setSubject($subject);
			
			$message = $this->params->get('message');
			$message = str_replace("{name}", $user['name'], $message);
			$message = str_replace("{username}", $user['username'], $message);
			$message = str_replace("{lastvisit}", $user['lastvisitDate'], $message);
			$message = str_replace("{replyto}", $config->getValue('config.mailfrom'), $message);
			$mailer->setBody($message);
			
			$sent = $mailer->Send();
			
			/* not needed for now because the mailer itself raises the error messages
			if($sent !== true){
				JFactory::getApplication()->enqueueMessage($sent->getMessage(), 'error');
			}
			*/
		}
		
	}

}
?>