<?php
namespace PpitFlow\Controller;

use PpitCore\Form\CsrfForm;
use PpitCore\Model\Account;
use PpitCore\Model\Context;
use PpitCore\Model\Document;
use PpitCore\Model\Csrf;
use PpitCore\Model\UserContact;
use PpitCore\Model\Vcard;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Http\Response\Stream;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class BlogController extends AbstractActionController
{
	public function indexAction()
	{
		// Retrieve context and parameters
		$context = Context::getCurrent();
		$place_identifier = $this->params()->fromRoute('place_identifier');
		if ($place_identifier) $place = Place::get($place_identifier, 'identifier');
		else {
			$place = $context->getPlace();
			$place_identifier = $place->identifier;
		}

		// Retrieve the blog entries for this place
		$documents = Document::getList('blog', ['place_id' => $place->id]);
//print_r($documents);
		if (!$documents) {
			$content = [
				[
					'img-src' => ['default' => '/img/p-pit/carlos-muza-hpjSkU2UYSU-unsplash.jpg'], 
					'img-alt' => ['default' => 'Laptop showing a dashboard'],
					'text' => ['default' => 
'
<!-- Category -->
<h6 class="font-weight-bold orange-text mb-3"><i class="fas fa-chart-line pr-2"></i>Stratégie marketing</h6>
<!-- Post title -->
<h3 class="dark-grey-text font-weight-bold mb-3"><strong>Présence sur le web ? Utilisation des réseaux sociaux ? Campagnes de mails ? Quelle stratégie numérique adopter ?</strong></h3>
<!-- Excerpt -->
<p class="dark-grey-text text-justify">Avec le web, tout le monde est virtuellement accessible c’est cool. Mais la contrepartie est que les frontières disparaissent, donc aussi les pré-carrés. Les distances s’annulent aussi vis-à-vis de nos concurrents, et les clients changent plus facilement de crèmerie. Conseils pratiques pour rester dans la course...</p>
'
					],
				]
			];
			$document = Document::instanciate();;
			$document->type = 'blog';
			$document->place_id = $place->id;
			$document->content = $content;
			$document->add();
			$documents = [$document];
		}

		// Account and commitments
		$accountType = $this->params()->fromRoute('accountType', 'generic');
		$profile = $context->getProfile();
		if (!$profile) {
			$profile = Account::instanciate($accountType);
		}
		
		// Authentication
		$panel = $this->params()->fromQuery('panel');
		$email = $this->params()->fromQuery('email');
		$error = $this->params()->fromQuery('error');
		$message = $this->params()->fromQuery('message');
		$redirect = $this->params()->fromQuery('redirect', 'home');
		if ($email && !$context->isAuthenticated()) {
			$vcard = Vcard::get($email, 'email');
			$profile->email = $email;
			if ($vcard) {
				$userContact = UserContact::get($vcard->id, 'vcard_id');
				if ($userContact) $panel = 'modalLoginForm';
				$profile->n_first = $vcard->n_first;
				$profile->n_last = $vcard->n_last;
			}
			else {
				$profile->n_first = $this->params()->fromQuery('n_first');
				$profile->n_last = $this->params()->fromQuery('n_last');
			}
			if ($panel != 'modalLoginForm') {
				$panel = 'modalRegisterForm';
			}
		}

		$view = new ViewModel(array(
			'context' => $context,
			'place_identifier' => $place_identifier,
			'documents' => $documents,
			'type' => $accountType,
			'profile' => $profile,
			'requestUri' => $this->request->getRequestUri(),
			'viewController' => 'ppit-flow/view-controller/blog-scripts.phtml',
			
			'token' => $this->params()->fromQuery('hash', null),
			'panel' => $panel,
			'email' => $email,
			'redirect' => $redirect,
			'message' => $message,
			'error' => $error,
		));
		$view->setTerminal(true);
		return $view;
	}
	
	public function newsletterAction()
	{
		// Retrieve context and parameters
		$context = Context::getCurrent();
		$mode = $this->params()->fromQuery('mode', 'unmarked');
		$locale = $this->params()->fromQuery('locale', 'default');

		$view = new ViewModel(array(
			'mode' => $mode,
			'locale' => $locale,
			'requestUri' => $this->request->getRequestUri(),
		));
		$view->setTerminal(true);
		return $view;
	}
	
	public function voeux2019prospectAction()
	{
		return $this->newsletterAction();
	}
}
