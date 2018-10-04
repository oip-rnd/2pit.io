<?php

return array(
	'specifications' => array(
			'credit' => array(
					'unlimitedCredits' => true,
			),
			'styleSheet' => array(
					'navbar' => 'navbar-default navbar-fixed-top',
					'panelHeadingBackground' => '#006179',
					'panelHeadingColor' => '#FFFFFF',
			),
			'bootstrap4' => array(
				'logo' => 'PBC-logo-web-fleur.png',
				'logo-height' => '40',
			),
			'headerParams' => array(
				'background-color' => '#79CCF3',
				'shift' => 0,
					'anchor' => array(
							'type' => 'route',
							'route' => 'public/home',
							'params' => [],
					),
					'logo' => 'PBC-logo-fleur-texte.png',
					'logo-height' => 40,
					'logo-href' => 'https://www.probonocorpo.com',
					'signature' => null,
					'advert' => 'probonocorpo.png',
					'advert-width' => 40,
				'self-powered' => true,
			),

		'menus/synapps' => array(
			'entries' => array(
				'contact' => array(
					'route' => 'account/indexAlt',
					'params' => array('entry' => 'contact', 'type' => 'pbc', 'app' => 'synapps'),
					'glyphicon' => 'glyphicon-user',
					'label' => array(
						'default' => 'All the contacts',
						'fr_FR' => 'Tous contacts',
					),
				),
				'account' => array(
					'route' => 'account/indexAlt',
					'params' => array('entry' => 'account', 'type' => 'pbc', 'app' => 'synapps'),
					'glyphicon' => 'glyphicon-user',
					'label' => array(
						'default' => 'Active',
						'fr_FR' => 'Actifs',
					),
				),
				'group' => array(
					'route' => 'account/indexAlt',
					'params' => array('entry' => 'group', 'type' => 'group', 'app' => 'synapps'),
					'glyphicon' => 'glyphicon-user',
					'label' => array(
						'default' => 'Groups',
						'fr_FR' => 'Groupes',
					),
				),
				'request' => array(
					'route' => 'event/indexAlt',
					'params' => array('type' => 'request', 'category' => 'request', 'app' => 'synapps'),
					'label' => array(
						'en_US' => 'Requests',
						'fr_FR' => 'Demandes',
					),
				),
				'survey_profile' => array(
					'route' => 'event/indexAlt',
					'params' => array('type' => 'course_test', 'category' => 'survey_profile', 'app' => 'synapps'),
					'label' => array(
						'en_US' => 'Course tests',
						'fr_FR' => 'Tests parcours',
					),
				),
				'ux_design' => array(
					'route' => 'event/indexAlt',
					'params' => array('type' => 'survey', 'category' => 'ux_design', 'app' => 'synapps'),
					'label' => array(
						'en_US' => 'Interviews',
						'fr_FR' => 'Interviews',
					),
				),
				'email' => array(
					'route' => 'event/index',
					'params' => array('type' => 'email', 'app' => 'synapps'),
					'label' => array(
						'en_US' => 'Emails',
						'fr_FR' => 'Emails',
					),
				),
			),
			'labels' => array(
				'default' => 'Back-office',
			),
		),

		'instance/charter' => array(
			'default' => '
<h4 class="dark-grey-text text-uppercase text-center mb-3">Probono corpo users’ charter</h4>
			
<h5 class="dark-grey-text font-weight-bold mb-3">Who are the stakeholders?</h5>

<p class="dark-grey-text">All users are SG employees, located anywhere in the world, whether internal employees or external ones with an SG employment contract, as well as interns, alternates and VIE.</p>
<p class="dark-grey-text"><strong>The contributor</strong>: an SG collaborator who wishes to help by proposing his/her expertise.</li>
<p class="dark-grey-text"><strong>The requestor</strong>: an SG collaborator who needs a one-time service.</li>
<p class="dark-grey-text">Users can be both contributor and requestor.</p>
<p class="dark-grey-text"><strong>The manager</strong>: s/he can be either contributor or requestor. Her/his employees may inform her/him of the help offered if they wish, but are not required to.</li>
<p class="dark-grey-text"><strong>Probono corpo</strong>: the platform that links requestors with contributors. ProBonoCorpo plays a trusted third-party role for all stakeholders.</li>
<p class="dark-grey-text"><strong>Help</strong>: a one-time service, in a specific domain of expertise, that does not involve too much of the contributor’s time.</li>
<hr>
<h5 class="dark-grey-text font-weight-bold mb-3">Charter of Commitment from the ‘contributor’ (I offer my help)</h5>
<ul class="fa-ul dark-grey-text">
<li><i class="fa-li fa fa-check text-success"></i>I pledge myself to respect Pro bono corpo’s values: volunteering in a ‘pro bono’ way (contribution), goodwill, cooperation, mutual aid, trust.</li>
<li><i class="fa-li fa fa-check text-success"></i>I pledge to neither ask for nor expect any counterpart in exchange for my help.</li>
<li><i class="fa-li fa fa-check text-success"></i>As a contributor, I have the responsibility to manage the time I give to Probono corpo with regards to my position and my manager:</i>
<li><i class="fa-li fa fa-check text-success"></i>I organise myself so that I complete the mission without penalising my position or my team (in case my team is struggling under a heavy workload, I ensure my manager agrees that I undertake the mission; failing this, I choose a more appropriate time to complete the mission),</li>
<li><i class="fa-li fa fa-check text-success"></i>If I am not / my team is not under a high workload situation, I have the choice to let my manager know, or not, of the mission I will undertake.</li>
<li><i class="fa-li fa fa-check text-success"></i>I can change my mind and say that I am actually not the right person for the mission, even after having had a first contact or having completed a first task.</li>
<li><i class="fa-li fa fa-check text-success"></i>Prior to the start of the mission: in case I am unable to fulfil my duty, or if I decide not to offer my help for any reason, I pledge to let Probono corpo know as soon as possible in order to find a solution. If I have already given my word to the requestor, I pledge to let him/her know as soon as possible.</li>
<li><i class="fa-li fa fa-check text-success"></i>During the ‘help’ mission: if the mission were to be stopped prior to its completion, I pledge to let ProBonoCorpo know as soon as possible in order to find a solution.</li>
<li><i class="fa-li fa fa-check text-success"></i>After completion of a mission, I give my impression through a ‘feedback’ to formalise what I have brought to the requestor and which good practices I have observed; I share these with ProBonoCorpo and potentially the requestor.</li>
<li><i class="fa-li fa fa-check text-success"></i>Right to confidentiality of profiles: a contributor may or may not authorise its profile’s publication (there is an explicit action to be carried out).</li>
<li><i class="fa-li fa fa-check text-success"></i>As user of the Pro bono corpo platform, I accept the Terms and Conditions of Use and I pledge to respect the requestor’s confidentiality with respect to his/her activity.</li>
<li><i class="fa-li fa fa-check text-success"></i>As member of Pro bono corpo, I partake in its community life.</li>
</ul></p>
<hr>
<h5 class="dark-grey-text font-weight-bold mb-3">Charter of Commitment from the ‘requestor’ (I need some help)</h5>
<ul class="fa-ul dark-grey-text">
<li><i class="fa-li fa fa-check text-success"></i>I pledge myself to respect ProBonoCorpo’s values: volunteering in a ‘pro bono’ way (contribution), goodwill, cooperation, mutual aid, trust.</li>
<li><i class="fa-li fa fa-check text-success"></i>I ensure my missions are in the form of a helping hand and do not exceed 2 hours (more than one helping hand can be asked for).</li>
<li><i class="fa-li fa fa-check text-success"></i>I welcome the contributor in the best possible way (e.g. by presenting my team and working space).</li>
<li><i class="fa-li fa fa-check text-success"></i>I ensure the ‘helping hand’ mission does not interfere with my team’s missions. I do not use the service simply in order to benefit from others’ skills for free.</li>
<li><i class="fa-li fa fa-check text-success"></i>I commit to not put the contributor in a risky situation (with regards to a sensitive client, sensitive data, a conflict of interest, and so on).</li>
<li><i class="fa-li fa fa-check text-success"></i>I ensure the difficulty level of the mission is adapted to the contributor.</li>
<li><i class="fa-li fa fa-check text-success"></i>I do not leave the contributor alone with the problem and especially not with an external client.</li>
<li><i class="fa-li fa fa-check text-success"></i>After completion of the mission, I thank the contributor and I offer to give him/her feedback.</li>
<li><i class="fa-li fa fa-check text-success"></i>Beware! If the (free) ProBonoCorpo helping hand is conducted for commercial purposes, I make sure to respect the associated regulation.</li>
<li><i class="fa-li fa fa-check text-success"></i>As user of the ProBonoCorpo platform, I accept the Terms and Conditions of Use and I respect the requestor’s confidentiality about his/her activity.</li>
<li><i class="fa-li fa fa-check text-success"></i>As member of ProBonoCorpo, I partake in its community life.</li>
<hr>
<h5 class="dark-grey-text font-weight-bold mb-3">Q&A for managers</h5>
<p>
<span class="text-secondary font-italic"><i class="fa fa-question-circle"></i>&nbsp;&nbsp;As a manager, should I authorise volunteering employees to give a helping hand in a ‘pro bono’ way, even though it is taking time from their work?</span>
<br><span class="dark-grey-text"><i class="fa fa-arrow-right"></i>&nbsp;&nbsp;Yes, even if participating in ProBonoCorpo is not mandatory. There is no legal or judicial issue regarding this, and it is normal in a working environment to take some time off work to help other colleagues.</span>
</p>
<p>
<span class="text-secondary font-italic"><i class="fa fa-question-circle"></i>&nbsp;&nbsp;Are working hours formalised / recorded and may I have information regarding my team(s)’ employees?</span>
<br><span class="dark-grey-text"><i class="fa fa-arrow-right"></i>&nbsp;&nbsp;ProBonoCorpo carries out continuous monitoring of its development’s KPIs; we are not considering communicating those indicators to managers because ProBonoCorpo is a collaborative platform, hence its use is based on trust and cooperation.</span>
</p>
<p>
<span class="text-secondary font-italic"><i class="fa fa-question-circle"></i>&nbsp;&nbsp;What are the benefits for my team(s)?</span>
<br><span class="dark-grey-text"><i class="fa fa-arrow-right"></i>&nbsp;&nbsp;There are various benefits that are directly linked to the development of skills, expertise and of the employees’ autonomy. By taking part in this action, you will contribute to making the collective gain in efficiency. You will also enable employees to take part in activities that are meaningful to them. Finally, you will increase their employability, whether employees are requestors or contributors.</span>
</p>
<p>
<span class="text-secondary font-italic"><i class="fa fa-question-circle"></i>&nbsp;&nbsp;Will my team find itself with more work, despite its not necessarily having the capacity to cope with it?</span>
<br><span class="dark-grey-text"><i class="fa fa-arrow-right"></i>&nbsp;&nbsp;It is not intended to be this way, but if a manager notices a drift, s/he can contact the ProBonoCorpo team who will take action to remind participants of the rules and good practices.</span>
</p>
<hr>			
<h5 class="dark-grey-text font-weight-bold mb-3">What are ProBonoCorpo’s commitments?</h5>

<p class="dark-grey-text">ProBonoCorpo, as a trusted third party, commits to:</p>
<ol class="dark-grey-text">
<li><strong>Revealing to the greatest number of employees</strong> (not only to HR or managers), <strong>their know-how and hidden talents</strong> in order to enable the sharing of their expertise and to enable them to become architects of their careers.</li>
<li><strong>Uniting the greatest number of employees and promoting their talent</strong>: creating and developing a mutual aid community, as well as listening to all in order to bring the SG divisions together.</li>
<li><strong>Creating connections between employees</strong> and enabling them to develop their network as well as allowing them to manage efficiently their time in order to find the right person at the right time.</li>
</ol>
<p class="dark-grey-text"><strong>Our added value</strong>: we attach great importance to the creation of a positive and attractive social framework for all of those who belong to our ecosystem.</p>
',
			'fr_FR' => '
<h4 class="dark-grey-text text-uppercase text-center mb-3">La charte des utilisateurs de <em>Probono corpo</em></h4>
			
<h5 class="dark-grey-text font-weight-bold mb-3">Qui sont les parties prenantes ?</h5>

<p class="dark-grey-text"><strong>Les utilisateurs</strong> : tous collaborateurs du groupe SG en France et à l’international, salariés (internes), externes en contrat SG (à confirmer), stagiaires / alternants / VIE.</p>
<p class="dark-grey-text"><strong>Contributeur</strong> : le collaborateur SG qui veut donner un coup de main en offrant son expertise.</li>
<p class="dark-grey-text"><strong>Demandeur</strong> : le collaborateur SG qui a besoin d’aide ponctuelle.</li>
<p class="dark-grey-text"><strong>Le manager</strong> : il peut être soit contributeur, soit demandeur. Ses collaborateurs peuvent l\'informer des coups de mains donnés ou reçus, mais ce n’est pas une obligation.</li>
<p class="dark-grey-text"><strong><em>Probono corpo</em></strong> : la plateforme de mise en relation. <em>Probono corpo</em> joue le rôle de tiers de confiance pour l’ensemble des parties prenantes.</li>
<p class="dark-grey-text font-italic">Mission = coup de main ponctuel et de courte durée donné sur le temps de travail dans un domaine d’expertise professionnel.</p>
<hr>
<h5 class="dark-grey-text font-weight-bold mb-3">Charte d’engagement du « contributeur » (j’offre un coup de main)</h5>
<ul class="fa-ul dark-grey-text">
<li><i class="fa-li fa fa-check text-success"></i>Je m’engage à respecter les valeurs de <em>Probono corpo</em> : volontariat en « pro bono » (don), bienveillance, coopération, entraide.</li>
<li><i class="fa-li fa fa-check text-success"></i>Je m’engage à ne pas demander / attendre de contrepartie du don réalisé.</li>
<li><i class="fa-li fa fa-check text-success"></i>En tant que contributeur, je suis responsable de gérer le temps donné à <em>Probono corpo</em> vis à vis de mon poste et de mon manager.</i>
<li><i class="fa-li fa fa-check text-success"></i>Je m’organise pour exécuter la mission sans pénaliser mon poste officiel ou mon équipe (en cas de charge de travail importante au niveau de l’équipe, je valide avec mon manager que le coup de main est possible, à défaut je définis la période la plus propice).</li>
<li><i class="fa-li fa fa-check text-success"></i>En dehors de période chargée, j’ai le choix d’informer ou pas mon manager du coup de main donné.</li>
<li><i class="fa-li fa fa-check text-success"></i>J’ai la possibilité de dire que je ne suis finalement pas la bonne personne pour donner le coup de main, même après un premier contact ou un premier coup de main.</li>
<li><i class="fa-li fa fa-check text-success"></i>Avant le début de la mission : en cas d’empêchement et si je renonce à donner le coup de main pour toutes raisons, je m’engage à prévenir <em>Probono corpo</em> le plus rapidement possible afin qu’une solution puisse être trouvée. Si j’ai déjà donné mon accord au demandeur, je m’engage à l’informer au plus vite.</li>
<li><i class="fa-li fa fa-check text-success"></i>Pendant la mission : dans l’éventualité d’interruption de la mission en cours, j’ai un droit de recours ; à ce titre, je m’engage à prévenir <em>Probono corpo</em> le plus rapidement possible afin qu’une solution puisse être trouvée.</li>
<li><i class="fa-li fa fa-check text-success"></i>A la fin de chaque mission, je réalise un bilan rapide (ou « feedback ») pour formaliser ce que j’ai pu apporter à l’équipe d’accueil et ce que j’ai observé comme bonnes pratiques ; je le partage avec <em>Probono corpo</em> et éventuellement avec le demandeur.</li>
<li><i class="fa-li fa fa-check text-success"></i>Droit à la confidentialité du profil : un contributeur a la possibilité de ne pas autoriser la publication du profil qu’il aura créé. La publication du profil est une action explicite du contributeur.</li>
<li><i class="fa-li fa fa-check text-success"></i>En tant qu’utilisateur de la plateforme PBC, j’accepte les Conditions Générales d’Utilisation et je m’engage à respecter la confidentialité liée à l’activité du demandeur.</li>
<li><i class="fa-li fa fa-check text-success"></i>Je deviens membre de la Communauté <em>Probono corpo</em> et je participe à son animation.</li>
</ul></p>
<hr>
<h5 class="dark-grey-text font-weight-bold mb-3">Charte d’engagement du « demandeur » (j’ai besoin d’aide)</h5>
<ul class="fa-ul dark-grey-text">
<li><i class="fa-li fa fa-check text-success"></i>Je m’engage à respecter les valeurs de <em>Probono corpo</em> : volontariat, bienveillance, coopération, entraide.</li>
<li><i class="fa-li fa fa-check text-success"></i>Je prévois des missions sous la forme de coups de main de 2 heures maximum (plusieurs coups de main sont possibles).</li>
<li><i class="fa-li fa fa-check text-success"></i>Je m’engage à réserver le meilleur accueil au contributeur (présentation de l’équipe, espace de travail).</li>
<li><i class="fa-li fa fa-check text-success"></i>Je m’assure que la mission liée au coup de main n’est pas en conflit avec celles de mon équipe.</li>
<li><i class="fa-li fa fa-check text-success"></i>Je m’engage à ne pas mettre le contributeur dans une situation risquée (relation face à un client sensible, données sensibles, etc.) (formulation à revoir).</li>
<li><i class="fa-li fa fa-check text-success"></i>Je m’assure que le niveau de difficulté de la mission est acceptable pour le contributeur.</li>
<li><i class="fa-li fa fa-check text-success"></i>Je ne laisse pas le contributeur seul face à un client externe.</li>
<li><i class="fa-li fa fa-check text-success"></i>Quand je reçois un don, je m’engage à remercier le contributeur.</li>
<li><i class="fa-li fa fa-check text-success"></i>A l’issue de la mission, je propose au contributeur de lui donner un feedback (facultatif dans la plateforme <em>Probono corpo</em>).</li>
<li><i class="fa-li fa fa-check text-success"></i>Attention ! si le coup de main gratuit, <em>Probono corpo</em> est réalisé dans un but commercial. Je m\'assure au préalable de la conformité de mes demandes.</li>
<li><i class="fa-li fa fa-check text-success"></i>En tant qu’utilisateur de la plateforme PBC, j’accepte les Conditions Générales d’Utilisation.</li>
<li><i class="fa-li fa fa-check text-success"></i>Je deviens membre de la Communauté <em>Probono corpo</em> et je participe à son animation.</li>
<hr>
<h5 class="dark-grey-text font-weight-bold mb-3">Q&A pour les managers</h5>
<p>
<span class="text-secondary font-italic"><i class="fa fa-question-circle"></i>&nbsp;&nbsp;En tant que manager, dois-je autoriser les collaborateurs volontaires pour donner des coups de main en « pro bono » à prendre des heures sur leur temps de travail ?</span>
<br><span class="dark-grey-text"><i class="fa fa-arrow-right"></i>&nbsp;&nbsp;La participation à <em>Probono corpo</em> est facultative. Il n’y a aucune problématique d’ordre légal ou juridique.</span>
</p>
<p>
<span class="text-secondary font-italic"><i class="fa fa-question-circle"></i>&nbsp;&nbsp;Est-ce que ces heures sont formalisées / enregistrées et puis-je avoir l’information s’agissant des collaborateurs de mon / mes équipe(s) ?</span>
<br><span class="dark-grey-text"><i class="fa fa-arrow-right"></i>&nbsp;&nbsp;<em>Probono corpo</em> réalise un suivi pour les KPI de son propre développement ; il n’est pas envisagé de diffuser ces indicateurs aux managers, sauf cas particulier.</span>
</p>
<p>
<span class="text-secondary font-italic"><i class="fa fa-question-circle"></i>&nbsp;&nbsp;Quels sont les bénéfices pour mes équipes ?</span>
<br><span class="dark-grey-text"><i class="fa fa-arrow-right"></i>&nbsp;&nbsp;Les bénéfices sont multiples ; ils sont directement liés au développement des compétences, de l’expertise, et de l’autonomie des collaborateurs. En s’engageant dans cette démarche de don, vous contribuez à ce que le collectif gagne en efficacité. Vous contribuez également à l’engagement des collaborateurs pour des activités faisant sens pour eux. Enfin, vous favorisez l’employabilité des collaborateurs de vos équipes, qu’ils soient demandeurs ou contributeurs.</span>
</p>
<p>
<span class="text-secondary font-italic"><i class="fa fa-question-circle"></i>&nbsp;&nbsp;Mon équipe va-t-elle se retrouver avec de la charge en plus alors qu’elle n’en a peut-être pas les moyens ?</span>
<br><span class="dark-grey-text"><i class="fa fa-arrow-right"></i>&nbsp;&nbsp;Ce n’est pas l’objectif, mais si un manager constate une dérive, il peut contacter l’équipe <em>Probono corpo</em> qui agira.</span>
</p>
<hr>			
<h5 class="dark-grey-text font-weight-bold mb-3">Quels sont les engagements de <em>Probono corpo</em> ?</h5>

<p class="dark-grey-text"><em>Probono corpo</em>, un <strong>tiers de confiance</strong> qui s’engage à (à afficher / publier sur le site) : </p>
<ol class="dark-grey-text">
<li><strong>Révéler les savoir-faire et les talents cachés des collaborateurs au plus grand nombre</strong> (et pas seulement à leur RH ou à leur Manager), pour mettre en valeur leur expertise et leur permettre d’être les artisans de leur carrière</li>
<li><strong>Rayonner et fédérer le plus grand nombre</strong> > constituer et développer une communauté d’entraide, être à l’écoute, pour désiloter la SG</li>
<li><strong>Mettre en relation les collaborateurs</strong> et développer leur réseau, leur faire gagner du temps pour trouver la bonne personne capable de répondre à leurs besoins</li>
<li><strong>Notre plus-value</strong> : nous mettons un point d’honneur à ce que le cadre social soit positif pour toutes les personnes engagées dans notre écosystème et à traiter autrui comme une fin et non comme un moyen.</li>
</ol>
',
		),

		'instance/general_terms_of_use' => array(
			'default' => '
<h4 class="dark-grey-text text-uppercase text-center mb-3">Terms of Use PROBONO CORPO</h4>
			
<h5 class="dark-grey-text font-weight-bold mb-3">Legal notice</h5>

<p class="dark-grey-text">This site is published by PROBONO CORPO, an internal startup for Société Générale SA benefit.</p>
<p class="dark-grey-text">
Société Anonyme capital 1009897175.75 on 11/12/2017<br>
Registered at the registry of Commercial Paris Chamber under the unique identification number B 552 120 222<br>
APE number: 651C<br>
Headquarter: 29 Boulevard Haussmann 75009 Paris<br>
Legal Representative: M. Frédéric OUDEA, CEO<br>
Publication director: Mme Caroline GUILLAUMIN<br>
Host: OVH 59820 Gravelines pour PROBONO CORPO / Société Générale 17 cours Valmy Paris La Défense
</p>
<p class="dark-grey-text">Contact : <a href="mailto:probonocorpo.par@socgen.com">probonocorpo.par@socgen.com</a></p>
			
<h5 class="dark-grey-text font-weight-bold mb-3">1 Purpose of the platform</h5>

<p class="dark-grey-text">Société Générale uses ProBonoCorpo to make available to its employees (Permanent contract, contractors and students in sandwich courses) owning an active professional address, an IT platform named « PROBONOCORPO ». The purpose of this platform is to allow Société Générale employees to (called hereafter « users ») to declare their preferences and skills. The aim is to create online profiles, which will be supplemented by suggested competences coming from the platform considering information provided.</p>
<p class="dark-grey-text">Users are invited to fill their profiles online providing complete, accurate and updated information’s which reflect their experiences, their current or prior competencies but also their preferences.</p>
<p class="dark-grey-text">Then, this platform proposes to each employee to connect with other employees in the SG group,according to their declared competencies.</p>
			
<h5 class="dark-grey-text font-weight-bold mb-3">2 Rules of use</h5>

<p class="dark-grey-text">The use of the platform « PROBONOCORPO » is facultative. The use of this platform must be carried out according to the following conditions, also to directives and instructions of Société Générale and others applicable rules in classification, confidentiality and information security areas. In addition to these rules, there are also “the classification and Information Protection Policy” (CIPP) Directive, “the classification and protection of information-confidentiality” policy (PCPI-C), the Rules of Procedure and its Charter for the use of Means of electronic communication, the code of conduct.</p>
<p class="dark-grey-text">Each user is responsible of his/her information declared on the platform. Users are thus bound by the internal rules recalled above to a duty of confidentiality on the information they hold and manipulate. They will have to ensure that the information declared on the platform does not violate these Internal rules. They must also respect the other users on the platform. Then, they are asked to communicate in a cordial and no rude way and especially:</p>
<ol class="dark-grey-text">
<li>Not to put online inappropriate contents including pornographic, pedophile, racist, xenophobic, defamatory content, which infringes human respect and its dignity, inciting the commission of an offence or a crime, contrary to public order and moral laws, which violate the image of Société Générale.</li>
<li>Not to commit reprehensible acts under the applicable law, in particular with regards to intellectual property and third-party rights, including personality rights. In this respect, Users must not upload third-party works or third-party representative works (photos, texts etc.) for which they do not hold the necessary rights of use or operating authorizations, or to reproduce trademarks for which they are not authorized depositaries and for which they do not hold rights of use.</li>
<li>Regardless of content put online by users on the platform, all brands, computer programs (firmware, software, computer developments, etc...), graphics, moving images or not, sounds, and more generally all or part of the "PROBONOCORPO" platform are likely to be protected by intellectual property rights and are and remain the exclusive property of Société Générale or the assigns to which Société Générale has obtained the necessary rights to their use.</li>
<li>Do not put online political, philosophic, trade-union, religious contents. Do not communicate information relating to private life of a third party without its writing agreement. Regarding the profile picture if it is provided, it must be centered on the user face and adapted to professional environment.</li>
</ol>
<p class="dark-grey-text">Data put online by users are susceptible to be moderated by the platform administrator and be removed whatever the reason, without the need to justify.</p>
			
<h5 class="dark-grey-text font-weight-bold mb-3">3 Protection of confidential information</h5>
			
<p class="dark-grey-text font-weight-bold mb-3">Collection</p>
<p class="dark-grey-text">As part of the use of the PROBONOCORPO platform, Société Générale is required to collect personal data about you as a processing manager.</p>
			
<p class="dark-grey-text font-weight-bold mb-3">Use</p>
<p class="dark-grey-text">Participation in this platform is optional, and the data collected is called to be used primarily for connecting employees across the SG group. The data collected are subject to computerized processing based on the legitimate interest of ProBonoCorpo and Société Générale.</p>
			
<p class="dark-grey-text font-weight-bold mb-3">Recipients</p>
<p class="dark-grey-text">The data is for the ProBonoCorpo platform administrator. Data stored on the platform will not be subject to any external communication except to third parties authorized under a legal or regulatory provision.</p>
			
<p class="dark-grey-text font-weight-bold mb-3">Duration of retention</p>
<p class="dark-grey-text">Unless disposition provided by law or regulation, personal data used for administrative management purposes are retained for the period of employment within the Société Générale group. More generically, the data collected are kept only for the duration necessary for the purposes of the working relationship and for the management of human resources, within the time limits periods in effect.</p>
			
<p class="dark-grey-text font-weight-bold mb-3">Processing safety</p>
<p class="dark-grey-text">The internal startup PROBONO CORPO takes the appropriate physical, technical and organizational measures to ensure the security and confidentiality of personal data, in particular to protect them against loss, accidental destruction, tampering, and unauthorized access.</p>
			
<p class="dark-grey-text font-weight-bold mb-3">Application of the European regulation on the transfer of personal data outside the European Economic Area</p>
<p class="dark-grey-text">No personal data transfer outside the European Economic Area will be carried out as part of the use of the PROBONOCORPO platform.</p>
			
<p class="dark-grey-text font-weight-bold mb-3">Exercise of rights</p>
<p class="dark-grey-text">The employee shall, under the conditions laid down in the applicable rules, have the following rights:</p>
<ol class="dark-grey-text">
<li>Access to the personal data collected concerning him/her;</li>
<li>Rectification or updating of inaccurate, incomplete or obsolete data;</li>
<li>Erasure;</li>
<li>Limitation;</li>
<li>Opposition for legitimate reasons;</li>
<li>Portability.</li>
</ol>
<p class="dark-grey-text">These rights can be exercised by e-mail at the following e-mail address: <a href="mailto:par.probonocorpo@socgen.com?suject=Pro bono corpo - Exercise of rights regarding personal data">par.probonocorpo@socgen.com</a></p>
			
<p class="dark-grey-text font-weight-bold mb-3">Informations</p>
<p class="dark-grey-text">For any question relating to the protection of their personal data, employees may contact the PROBONO CORPO Data Protection officer at the address <a href="mailto:"par.probonocorpo@socgen.com">par.probonocorpo@socgen.com</a>. Employees also have the possibility to submit a complaint to the supervisory authority in charge of data protection.</p>
			
<h5 class="dark-grey-text font-weight-bold mb-3">4 Right of disconnection</h5>
<p class="dark-grey-text">The platform is accessible on laptops, smartphones, and tablets. Société Générale recalls the right to disconnecting the employee allowing him to reconcile professional and private life. It is therefore not required to connect outside of its working time. The user who outside his working time would use the platform on his own initiative would be performing a professional activity at the request of the company.</p>
			
<h5 class="dark-grey-text font-weight-bold mb-3">5 Modification of Terms of Use</h5>
<p class="dark-grey-text">The user is invited to take regular notice of these Terms of Use, which can be changed at any time.</p>
			
<h5 class="dark-grey-text font-weight-bold mb-3">6 Applicable law / Dispute</h5>
<p class="dark-grey-text">The present general conditions are subject to French law.</p>
			
<h5 class="dark-grey-text font-weight-bold mb-3">7 Suspension or closure of access to the platform</h5>
<p class="dark-grey-text">Société Générale reserves the option of shutting down access to the PROBONOCORPO platform at any time and for any reason without having to justify it or suspend a user’s access to this system, in case of non-compliance with the general conditions of use. If a user leaves Société Générale, his/her account will be disabled. His/her profile will be kept and can be deleted on his/her request.</p>
			
<h5 class="dark-grey-text font-weight-bold mb-3">8 Miscellaneous</h5>
<p class="dark-grey-text">Requests for support or further information on the use of the platform should be sent to the following e-mail address: <a href="mailto:"par.probonocorpo@socgen.com">par.probonocorpo@socgen.com</a>. Elsewhere, if the user notices content that should not be displayed on PROBONOCORPO for any reason, including because it does not comply with Société Générale’s policy, please contact the ProBonoCorpo team at <a href="mailto:"par.probonocorpo@socgen.com?subject=SUSPICIOUS CONTENT">par.probonocorpo@socgen.com</a> with the topic “SUSPICIOUS CONTENT".</p>
',
			'fr_FR' => '
<h4 class="dark-grey-text text-uppercase text-center mb-3">Conditions Générales d’Utilisation (CGU) de Pro bono corpo</h4>
			
<h5 class="dark-grey-text font-weight-bold mb-3">Mentions légales</h5>

<p class="dark-grey-text">Ce site est publié par Pro bono corpo, une startup interne pour le compte de Société Générale SA.</p>
<p class="dark-grey-text">
Société Anonyme au capital de 1009897175,75 au 11/12/2017<br>
Immatriculée au RCS Paris sous le numéro unique d’identification B 552 120 222<br>
Numéro APE : 651C<br>
Siège social : 29 Boulevard Haussmann 75009 Paris<br>
Représentant légal : M. Frédéric OUDEA, Directeur Général<br>
Directeur de la publication : Mme Caroline GUILLAUMIN<br>
Hébergeur : OVH 59820 Gravelines pour Pro bono corpo / SOCIETE GENERALE 17 cours Valmy Paris La Défense
</p>
<p class="dark-grey-text">Contact: <a href="mailto:probonocorpo.par@socgen.com">probonocorpo.par@socgen.com</a></p>
			
<h5 class="dark-grey-text font-weight-bold mb-3">1 Objet de la plateforme</h5>

<p class="dark-grey-text">SOCIETE GENERALE a recours à la startup interne Pro bono corpo pour mettre à disposition de ses collaborateurs (CDI, CDD et alternants) disposant d’une adresse professionnelle active, une plateforme intitulée « Pro bono corpo ». L’objectif de cette plateforme est de permettre aux collaborateurs de Société Générale (ci-après « Utilisateur(s) ») de déclarer à leur convenance leurs compétences et appétences professionnelles à des fins de création de profils en ligne, qui pourront être enrichis par des suggestions de compétences proposées par la plateforme au regard des informations renseignées.</p>
<p class="dark-grey-text">Les utilisateurs sont invités à renseigner leur profil en fournissant des informations complètes, précises et à jour qui reflètent de manière fidèle leurs compétences actuelles ou antérieures ainsi que leurs appétences.</p>
<p class="dark-grey-text">Partant, cette plateforme propose de façon personnalisée des mises en relation avec d’autres collaborateurs dans le groupe et ce, en adéquation avec les compétences déclarées.</p>
			
<h5 class="dark-grey-text font-weight-bold mb-3">2 Règles d’utilisation</h5>

<p class="dark-grey-text">L’utilisation de la plateforme Pro bono corpo est facultative. L’utilisation de cette plateforme doit s’effectuer conformément aux conditions ci-après ainsi qu’aux Directives et Instructions de SOCIETE GENERALE et aux autres règles internes applicables en matière de classification, de confidentialité et de protection de l’information, y compris, la Directive sur la Politique de Classification et de Protection de l’Information (PCPI), l’Instruction sur la Politique de Classification et de Protection de l’Information - Confidentialité (PCPI-C), le Règlement Intérieur et sa charte d’utilisation des moyens de communication électronique, le code de conduite.</p>
<p class="dark-grey-text">Chaque utilisateur est responsable des informations qu’il est amené à déclarer sur la plateforme. Les utilisateurs sont ainsi tenus au titre des règles internes rappelées ci-dessus à un devoir de confidentialité sur les informations qu’ils détiennent et manipulent, ils devront veiller à ce que les informations déclarées sur la plateforme ne violent pas ces règles internes. Ils doivent également respecter les autres utilisateurs de la plateforme. Ainsi, il leur demandé de communiquer de façon cordiale et non grossière et notamment :</p>
<ol class="dark-grey-text">
<li>Ne pas mettre en ligne des contenus inappropriés notamment à caractère pornographique, pédophile, raciste, xénophobe, diffamatoire, portant atteinte au respect de la personne humaine et à sa dignité, incitant à la commission d’un délit ou d’un crime, contraires à l’ordre public ou aux bonnes mœurs, attentatoires à l’image de marque de SOCIETE GENERALE.</li>
<li>Ne pas commettre des actes répréhensibles au regard de la loi applicable, notamment en ce qui concerne la propriété intellectuelle et les droits de tiers, dont les droits de la personnalité. Par exemple, les utilisateurs ne doivent pas mettre en ligne des œuvres de tiers et/ou représentant des tiers (photos, textes, etc.) pour lesquelles ils ne détiennent pas les droits d’utilisation ou les autorisations d’exploitation nécessaires, ou reproduire des marques dont ils ne sont pas dépositaires et pour lesquelles ils ne détiennent pas de droits d’utilisation.
Indépendamment des contenus mis en ligne par les utilisateurs sur la plateforme, l’ensemble des marques, programmes informatiques (progiciels, logiciels, développements informatiques, etc.), graphiques, images animées ou non, sons, et plus généralement tout ou partie de la plateforme PROBONO CORPO sont susceptibles d’être protégés par des droits de propriété intellectuelle et sont et demeurent la propriété exclusive de Société Générale ou des ayant-droits auprès desquels Société Générale a obtenu les droits nécessaires à leur utilisation. </li>
<li>Ne pas mettre en ligne de contenu lié à une activité d’ordre politique, philosophique, syndical ou religieux. Ne doivent pas être communiquées les informations relatives à la vie privée d’un tiers sans son accord écrit. S’agissant de la photo du profil si elle est renseignée, elle doit être centrée sur le visage de l’utilisateur et adaptée à l’environnement professionnel.</li>
</ol>
<p class="dark-grey-text">Les données mises en ligne par les utilisateurs sont susceptibles d’être modérées par l’administrateur de la plateforme et d’être supprimées pour quelque raison que ce soit, sans nécessité de le justifier.</p>
			
<h5 class="dark-grey-text font-weight-bold mb-3">3 Protection des données à caractère personnel</h5>
			
<p class="dark-grey-text font-weight-bold mb-3">Collecte</p>
<p class="dark-grey-text">Dans le cadre de l’utilisation de la plateforme Pro bono corpo, SOCIETE GENERALE est amenée à collecter des données personnelles vous concernant en qualité de responsable de traitement.</p>
			
<p class="dark-grey-text font-weight-bold mb-3">Utilisation</p>
<p class="dark-grey-text">La participation à cette plateforme est facultative, et les données collectées sont appelées à être utilisées principalement pour mettre en relation les collaborateurs du groupe SOCIETE GENERALE. Les données collectées font l’objet de traitements informatisés sur la base de l’intérêt légitime de la startup Pro bono corpo et de SOCIETE GENERALE.</p>
			
<p class="dark-grey-text font-weight-bold mb-3">Destinataires</p>
<p class="dark-grey-text">Les données sont destinées à l’administrateur de la plateforme Pro bono corpo. Les données enregistrées sur la plateforme ne feront l’objet d’aucune communication extérieure sauf aux tiers autorisés en vertu d’une disposition légale ou réglementaire.</p>
			
<p class="dark-grey-text font-weight-bold mb-3">Durée de conservation</p>
<p class="dark-grey-text">Sauf dispositions législatives ou réglementaires contraires, les données à caractère personnel utilisées aux fins de gestion administrative sont conservées le temps de la période d’emploi au sein du groupe SOCIETE GENERALE. De manière plus générique, les données collectées sont conservées uniquement pendant la durée nécessaire à l’accomplissement des finalités relatives à la relation de travail et à la gestion des ressources humaines, dans la limite des délais de prescription en vigueur.</p>
			
<p class="dark-grey-text font-weight-bold mb-3">Sécurité des traitements</p>
<p class="dark-grey-text">La startup Pro bono corpo prend les mesures physiques, techniques et organisationnelles appropriées pour assurer la sécurité et la confidentialité des données à caractère personnel, en vue notamment de les protéger contre toute perte, destruction accidentelle, altération, et accès non autorisés.</p>

<p class="dark-grey-text font-weight-bold mb-3">Application de la réglementation européenne en matière de transferts de données à caractère personnel en dehors de l\'Espace Économique Européen</p>
<p class="dark-grey-text">Aucun transfert de données à caractère personnel en dehors de l’Espace Economique Européen ne sera effectué dans le cadre de l’utilisation de la plateforme Pro bono corpo.</p>
			
<p class="dark-grey-text font-weight-bold mb-3">Exercice des droits</p>
<p class="dark-grey-text">Le collaborateur dispose, dans les conditions prévues par la réglementation applicable, des droits suivants :</p>
<ol class="dark-grey-text">
<li>Accès aux données à caractère personnel collectées le concernant ;</li>
<li>Rectification ou mise à jour des données inexactes, incomplètes ou périmées ;</li>
<li>Effacement ;</li>
<li>Limitation ;</li>
<li>Opposition pour motifs légitimes ;</li>
<li>Portabilité.</li>
</ol>
<p class="dark-grey-text">Ces droits peuvent être exercés par messagerie électronique à l’adresse mail suivante : <a href="mailto:"par.probonocorpo@socgen.com">par.probonocorpo@socgen.com</a></p>
			
<p class="dark-grey-text font-weight-bold mb-3">Informations</p>
<p class="dark-grey-text">Pour toute question relative à la protection de leurs données personnelles, les collaborateurs peuvent s’adresser au Délégué à la Protection des Données de Pro bono corpo à l’adresse <a href="mailto:"par.probonocorpo@socgen.com">par.probonocorpo@socgen.com</a>. Les collaborateurs ont également la possibilité d’introduire une réclamation auprès de l’autorité de contrôle en charge de la protection des données.</p>
			
<h5 class="dark-grey-text font-weight-bold mb-3">4 Droit à la déconnexion</h5>
<p class="dark-grey-text">La plateforme étant accessible sur les ordinateurs portables, smartphone, et tablettes, SOCIETE GENERALE rappelle le droit à la déconnexion du collaborateur lui permettant de concilier vie professionnelle et vie privée. Il n’est en conséquence pas tenu de se connecter en dehors de son temps de travail. L’utilisateur qui en dehors de son temps de travail utiliserait la plateforme de sa propre initiative ne saurait être considéré comme effectuant une activité professionnelle à la demande de l’entreprise.</p>
			
<h5 class="dark-grey-text font-weight-bold mb-3">5 Modification des conditions d’utilisation</h5>
<p class="dark-grey-text">L’Utilisateur est invité à prendre régulièrement connaissance des présentes CGU, qui peuvent être modifiées à tout moment.</p>
			
<h5 class="dark-grey-text font-weight-bold mb-3">6 Loi applicable / litiges</h5>
<p class="dark-grey-text">Les présentes conditions générales sont soumises au droit français.</p>
			
<h5 class="dark-grey-text font-weight-bold mb-3">7 Suspension ou Fermeture de l’accès à la Plateforme</h5>
<p class="dark-grey-text">SOCIETE GENERALE se réserve la possibilité de fermer l’accès à la plateforme Pro bono corpo à tout moment et pour quelque raison que ce soit sans avoir à le justifier, ou de suspendre l’accès d’un Utilisateur à ce système, notamment en cas de non-respect des conditions générales d’utilisation. Si un utilisateur quitte Société Générale son compte sera désactivé. Son profil sera conservé pour une durée de 1 an maximum et pourra être supprimé sur sa demande.</p>
			
<h5 class="dark-grey-text font-weight-bold mb-3">8 Divers</h5>
<p class="dark-grey-text">Les demandes de support ou d’informations complémentaires sur l’utilisation de la plateforme doivent être envoyées à l’adresse mail suivante : <a href="mailto:"par.probonocorpo@socgen.com">par.probonocorpo@socgen.com</a>. Par ailleurs, si l’utilisateur constate un contenu qui ne devrait pas être présent sur Pro bono corpo pour quelque raison que ce soit, y compris parce qu’il n’est pas conforme aux règles Groupe SOCIETE GENERALE, il est invité à contacter sans délai <a href="mailto:"par.probonocorpo@socgen.com?subject=CONTENU SUSPECT">par.probonocorpo@socgen.com</a> avec le sujet « CONTENU SUSPECT ».</p>
			',
		),
		
		'manageable_roles' => ['admin', 'sales_manager'],
		
		'landing_account_type' => 'pbc',
		
		'core_account/generic/property/property_1' => array(
			'mandatory' => false,
			'definition' => 'inline',
			'type' => 'select',
			'multiple' => true,
			'modalities' => array(
				'contributor' => ['default' => 'Contributor', 'fr_FR' => 'Contributeur'],
				'requestor' => ['default' => 'Requestor', 'fr_FR' => 'Demandeur'],
			),
			'labels' => ['default' => 'Role', 'fr_FR' => 'Rôle'],
		),

		'core_account/generic/property/property_2' => array(
			'mandatory' => false,
			'definition' => 'inline',
			'type' => 'select',
			'multiple' => true,
			'modalities' => array(
				'available' => ['default' => 'Available for a phone call', 'fr_FR' => 'Disponible pour un point téléphonique'],
			),
			'labels' => ['default' => 'Availability', 'fr_FR' => 'Disponibilité'],
		),
		
		'core_account/generic/property/comment_1' => array(
			'definition' => 'inline',
			'type' => 'textarea',
			'labels' => array(
				'default' => 'Offered skills',
				'fr_FR' => 'Compétences offertes',
			),
			'max_length' => 65535,
		),
		
		'core_account/generic/property/comment_2' => array(
			'definition' => 'inline',
			'type' => 'textarea',
			'labels' => array(
				'default' => 'Requested skills',
				'fr_FR' => 'Compétences demandées',
			),
			'max_length' => 65535,
		),
		
		'core_account/sendMessage' => array(
/*			'templates' => array(
				'emailing_survey_isc' => array('definition' => 'emailing_isc/probonocorpo'),
				'emailing_survey_intrapreneurs' => array('definition' => 'emailing_intrapreneurs/probonocorpo'),
				'emailing_reminder_suspects' => array('definition' => 'emailing_reminder_suspects/probonocorpo'),
				'emailing_adopt1projet' => array('definition' => 'emailing_adopt1projet/probonocorpo'),
			),*/
			'themes' => array(
				'theme_1' => array('definition' => 'customization/pbc/send-message/theme_1'),
				'theme_2' => array('definition' => 'customization/pbc/send-message/theme_2'),
			),
			'signature' => array(
				'definition' => 'inline',
				'body' => array(
					'default' => '
<hr>
<div><a href="https://www.probonocorpo.com"><img src="http://img.probonocorpo.com/PBC-logo-fleur-texte.png" width="300" height="79" alt="Probono corpo logo" /></a></div>
<br />The <strong>Probono corpo</strong> team
<br />Bruno, Daniel, Gis&egrave;le, Nicole
<br /><a href="https://sbc.safe.socgen/groups/pro-bono-corpo">go/probono</a>
<br /><a href="mailto:probonocorpo.par@socgen.com">probonocorpo.par@socgen.com</a>
					',
					'fr_FR' => '
<hr>
<div><a href="https://www.probonocorpo.com"><img src="http://img.probonocorpo.com/PBC-logo-fleur-texte.png" width="300" height="79" alt="Probono corpo logo" /></a></div>
<br />L\'&Eacute;quipe <strong>Probono corpo</strong>
<br />Bruno, Daniel, Gis&egrave;le, Nicole
<br /><a href="https://sbc.safe.socgen/groups/pro-bono-corpo">go/probono</a>
<br /><a href="mailto:probonocorpo.par@socgen.com">probonocorpo.par@socgen.com</a>
',
					),
			),
		),

		'event/event/property/matched_accounts' => array(
			'definition' => 'inline',
			'type' => 'multiselect',
			'account_type' => 'pbc',
			'labels' => array(
				'en_US' => 'Matched accounts',
				'fr_FR' => 'Comptes connectés',
			),
		),
		
		'flow/tests' => array(
			'test_request' => 'test_request/probonocorpo',
			'survey_profile' => 'survey_profile/probonocorpo',
		),
		
		'mailTo' => 'contact@probonocorpo.com', // Deprecated
/*		'core_account/mailTo' => array( // Overrides the target emails if not NULL (for test purposes or manual email sending)
			'contact@probonocorpo.com' => 'ProBonoCorpo',
			'probonocorpo.par@socgen.com' => 'ProBonoCorpo',
		),*/
	),
);
