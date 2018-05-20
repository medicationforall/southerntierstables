<?php
//create session variable
$sitename = 'sts';

	if(empty($_SESSION['site']) || strcmp($_SESSION['site']->getSettings()->getSite(),$sitename)!=0)
	{
		Core::debug('site is empty');

		$site = new Page('Southern Tier Stables','./starter/style.css');
		$site->addStyle('sts.css');

		$settings = $site->getSettings();
		$settings->setSite($sitename);
		$settings->setScriptBase('./framework/script');
		$site->setFavIcon('true');

		//setup initial meta information
		$site->meta('Author','Nicole Brink-Ramey');
		$site->meta('Developer','James M Adams');
		$site->meta('viewport','width=device-width, initial-scale=1');

		$site->setConnect(new Connect('root','root','localhost','jcale0_frameworksts'));

		$site->script('jquery-*.min.js');
		$site->script('jquery-ui-*.custom.min.js');
		$site->script('isInViewport.min-*.js');
		$site->script('dialog.js');

		$site->getSettings()->setEmail('support@southerntierstables.com');
		$site->getAccount()->setSalt('GENERIC_SALT_REPLACE_THIS');

		$site->setScriptsTop(false);

		$account = $site->getAccount();

		$login = new LoginControl();

		$pageContainer = new Panel('pageContainer');
		$pageContainer->setUnique('pageContainer');

		$header= new Panel('header');
		$header->setUnique('header');

		$headerText = new Code('<span class="title">Southern Tier Stables</span><br /> and<br /> Therapeutic Riding Center');

		$header->add($headerText);

		$centerContainer = new Panel('centerContainer');
		$centerContainer->setUnique('centerContainer');

		$bar = new Panel('bar');
		$bar->setUnique('bar');

		$nav = new Panel('navigation');
		$nav->setUnique('navigation');

		$home = new Menu('Home','index.php');
		$services = new Menu('Services','services.php');
		$services->setUnique('services');
		$facilities = new Menu('Facilities','facilities.php');
		$memories = new Menu('Makin\' Memories','makinmemories.php');
		$memories->setUnique('memories');
		$events = new Menu('Events','events.php');
		$gallery = new Menu('Gallery','gallery.php');
		$about = new Menu('About','about.php');
		$about->setUnique('about');
		$policy = new Menu('Policies &amp; Forms','policiesandforms.php');
		$contact = new Menu('Contact','contact.php');

		//services

		$ridingLessons = new Menu('Riding Lessons','ridinglessons.php');
		$trailRiding = new menu('Trail Riding','trailriding.php');
		$horseBoarding = new menu('Horse Boarding','horseboarding.php');
		$horseStartingAndTraining = new menu('Horse Starting And Training','horsestartingandtraining.php');
		$birthdayPonyRides = new menu('Birthday Pony Rides','birthdayponyrides.php');
		$tackShop = new Menu('Tack Shop','tackshop.php');
		$camping = new Menu('Camping','camping.php');
		$pe129 = new Menu('PE 129','pe129.php');
		$ponyRides = new Menu('Pony Rides','ponyrides.php');
	
		$programs = new Menu('Programs','programs.php');
		$programs->setUnique('programs');
		$summer = new Menu('Summer Day Camp','summer.php');
		$schoolPrograms = new Menu('Makin\' Memories School Programs','schoolPrograms.php');
		$cills = new Menu('CILLS Program','cills.php');
		$riders = new Menu('Freedom Riders','riders.php');
		$hillside = new Menu('Hillside Program','hillside.php');
		$scouts = new Menu('Girl Scouts','scouts.php');

		$news = new Menu('News','news.php');
		$horses = new Menu('Our Horses','ourhorses.php');
		$mudRun = new Menu('Annual Mud Run','mudrun.php');
		$volunteer = new Menu('Volunteer','volunteer.php');
		$donate = new Menu('Donate','donate.php');

		$services->add($ridingLessons);
		$services->add($trailRiding);
		$services->add($horseBoarding);
		$services->add($horseStartingAndTraining);
		$services->add($camping);
//		$services->add($pe129);
		$services->add($tackShop);
		$services->add($birthdayPonyRides);
		$services->add($ponyRides);
		
		

		$about->add($news);
	//	$memories->add($mudRun);
		$memories->add($horses);
		$memories->add($volunteer);
		$memories->add($donate);
		
		$programs->add($schoolPrograms);
		$programs->add($summer);

		$programs->add($cills);
		$programs->add($riders);
		$programs->add($hillside);

		$programs->add($scouts);
		$programs->add($pe129);

		$nav->add($home);
		$nav->add($memories);
		$nav->add($programs);
		$nav->add($services);
		$nav->add($facilities);
		$nav->add($events);
		$events->setUnique('events');
			$competitions = new Menu('Competitions','competitions.php');
			$events->add($competitions);

			$shows = new Menu('Shows','shows.php');
			$events->add($shows);

			$dinnerShows = new Menu('Dinner Shows','dinnerShows.php');
			$events->add($dinnerShows);

		$nav->add($gallery);
		$nav->add($about);
		$nav->add($policy);
		$nav->add($contact);


		$center = new Panel('center');
		$center->setUnique('center');

		$footer= new Panel('footer');
		$footer->setUnique('footer');

		$copyright = new Code('&copy; 2015 - '.date('Y').' Southern Tier Stables');

		$footer->add($copyright);



		$site->add($login);
		$site->add($pageContainer);
		$pageContainer->add($header);
		$pageContainer->add($centerContainer);
		$centerContainer->add($nav);
		$centerContainer->add($bar);
		$centerContainer->add($center);
		$site->add($footer);

		//APPS
		$gallery = new Gallery2('','image/gallery');
		$gallery->addTagToIgnore('archive');
		$gallery->setRefPage('gallery.php');
		$gallery->setThumbSize(140);
		$gallery->setThumbCompression(40);
		$site->addApp('gallery2',$gallery);

		//create calendar
		$calendar = new Calendar('','month','calendar');
		//$calendar->setEventClass('CmpEvent');
		$calendar->setRefPage('events.php');
		$site->addApp('calendar',$calendar);

		$_SESSION['site']=$site;
	}
	else
	{
		Core::debug('site is not empty');
	}
?>
