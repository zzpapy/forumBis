<?php
    namespace Controller;

    use Model\Managers\MembreManager;
    use Model\Managers\SujetManager;
    use Model\Managers\MessageManager;
    use Model\Managers\SubMessManager;
    use Model\Managers\SignalementManager;
    use APP\SESSION;
    use APP\Upload;
    
    
    class HomeController{

        public function index(){
           

            return [
                "view" =>  VIEW_DIR."sujet.php"];
        }
       

        public function sujet(){
           
           $findSujet = new SujetManager();
            
            $sujets =$findSujet->findAll();
            $tab = [];
            if(is_object($sujets)){
                $man = new MessageManager();
                $mess = $man->findBySujet($sujets->getId()); 
                if($mess && !is_object($mess)){
                    $tab[$sujets->getId()] = count($mess);
                    
                }
                else if(is_object($mess)){
                    $tab[$sujets->getId()] = 1;
                }
                else{
                    $tab[$sujets->getId()] = 0;
                }
                
            }
            else if($sujets != ''){
                foreach ($sujets as $key => $value) {
                    $man = new MessageManager();
                    $mess = $man->findBySujet($value->getId());
                    if($mess && !is_object($mess)){
                        $tab[$value->getId()] = count($mess);
                    }
                    else if(is_object($mess)){
                        $tab[$value->getId()] = 1;
                    }
                    else{
                        $tab[$value->getId()] = 0;
                    }
                }
            }
            $toto = new MembreManager();
            $connected = $toto->findConnected(1);
            var_dump($connected);die;
            $test = $toto->selectUsers();
             
            $signal = new SignalementManager();
            $signal = $signal->findAll();
            SESSION::addFlash("connected",$connected);
            SESSION::addFlash( "liste",$sujets);
            SESSION::addFlash( "mess",$tab);
            SESSION::addFlash( "users",$test);
            SESSION::addFlash( "signal",$signal);
    
            return [
                "view" => VIEW_DIR."sujet.php",
                "data" =>""
            ];
        }
        public function signIn(){

            return [
                "view" =>  VIEW_DIR."crea-compte.php"
            ];
        }
        public function crea($data){
            $man = new MembreManager($_POST);
            $user = $man -> findOneByName($data["pseudo"]);
            if($user){
                echo "le pseudo est déjà utilisé";
                return [
                    "view" => VIEW_DIR."crea-compte.php",
                    "data" => ""
                ];
            }
            else{
                $user  = $man -> add($_POST);
                return [
                    "view" => VIEW_DIR."sujet.php",
                    "data" => ""
                ];
            }
        }
        public function connect(){
            $man = new MembreManager();
            $user = $man -> findOneByName($_POST["pseudo"]);
            $bool = false;
            if($user){
                    if( password_verify($_POST["password"], $user->getPassword()) && $_POST["pseudo"] == $user->getPseudo()){
                        $bool = true;
                        SESSION::addFlash("user",$user);
                        if($user->getPseudo() == "zzpapy"){
                            SESSION::addFlash("admin",1);
                            $msg = "Vous êtes maintenant connecté";
                            SESSION::addFlash("success",$msg);
                        }
                        
                        
                        $man = new SujetManager();
                        $sujets = $man->findAll(); 
                        $action = "crea_sujet"; 
                        if(is_object($sujets)){
                            $man = new MessageManager();
                            $mess = $man->findBySujet($sujets->getId());
                            if($mess && !is_object($mess)){
                                $tab[$sujets->getId()] = count($mess);
                            }
                            else if(is_object($mess)){
                                $tab[$sujets->getId()] = 1;
                            }
                            else{
                                $tab[$sujets->getId()] = 0;
                            }
                            
                        }
                        else{                            
                            foreach ($sujets as $key => $value) {
                                $man = new MessageManager();
                                $mess = $man->findBySujet($value->getId()); 
                                if($mess && !is_object($mess)){
                                    $tab[$value->getId()] = count($mess);
                                }
                                else if(is_object($mess)){
                                    $tab[$value->getId()] = 1;
                                }
                                else{
                                    $tab[$value->getId()] = 0;
                                }
                            }
                        }
                        $test = new MembreManager();
                        $connect = $test->updateConnect($user->getId());
                        $connected = $test->findConnected();
                        $test = $test->selectUsers();
                        // var_dump($connected);die;
                        // var_dump($test);
                        $user ->setPassword("");
                        SESSION::addFlash( "connected",$connected);
                        SESSION::addFlash( "bool",$bool);
                        SESSION::addFlash( "user",$user);
                        SESSION::addFlash( "liste",$sujets);
                        SESSION::addFlash( "mess",$tab);
                        SESSION::addFlash( "users",$test);
                        SESSION::addFlash( "connect",true);
                            
                        header('location:index.php?action=sujet');die();
                        }                   
                        else{
                            $msg = "Une erreur s'est produite merci de vérifier vos éléments de connexion";
                            SESSION::addFlash("error",$msg);
                            
                            return [
                                "view" => VIEW_DIR."sujet.php" ,
                                "data" => "ce compte n'existe pas"                       
                            ];
                        }
                    }
                    else{
                        $msg = "Une erreur s'est produite merci de vérifier vos éléments de connexion";
                        SESSION::addFlash("error",$msg);
                        return [
                            "view" => VIEW_DIR."sujet.php" ,
                    "data" => "le mot de passe ou le pseudo est incorrect !!!"                       
                ];
            }   
        }
        public function logout(){
            $test = new MembreManager();
            $connect = $test->updateUnConnect($_SESSION["user"]->getId());
            $connected = $test->findConnected();
            SESSION::addFlash("connected",$connected);
            // $test = $test->selectUsers();
            unset($_SESSION["user"]);
            unset($_SESSION["admin"]);
            unset($_SESSION["connect"]);
            var_dump($_SESSION);die;
            // var_dump($test);
                header('location:index.php?action=index');
        }
        public function crea_sujet($id){
            if($_FILES["photo"]["name"] != ''){
                $photo = $this->upload($_FILES);
                $_POST["photo"] = $photo;
            }
            $man = new SujetManager();
            $sujet = $man->add($_POST);  
            $sujets = $man->findAll(); 
            if($sujet != ""){
                $msg = "Un nouveau sujet viens d'être créer";
                SESSION::addFlash("success",$msg);
                SESSION::addFlash("liste",$sujets);
            }
            header(('location:index.php?action=crea_mess&membre_id='.$_SESSION["user"]->getId().'&sujet_id='.$sujet.''));
            die();
        }
        public function crea_mess($id){
            
            if(isset($_SESSION["user"])){
                if(isset($_GET["close"])){
                    $close = $_GET["close"];
                }
                else{
                    $close = 0;
                }
                if($_GET["sujet_id"] != '' && !isset($_GET["ok"]) && $close == 0){
                    $man = new SujetManager();
                    
                    if($_FILES["photo"]["name"] != ''){
                        $photo = $this->upload($_FILES);
                        $_POST["photo"] = $photo;
                    }
                    $sujets = $man->findAll(); 
                    $sujet = $man->findOneById($_GET["sujet_id"])->getTitre();
                    $sub = new SubMessManager();
                    $sub_mess = $sub->findAll();
                    $man = new MessageManager();
                    $log = $man->add($_POST);
                    if($log != ""){
                        $msg = "Un nouveau message viens d'être créer";
                        SESSION::addFlash("success",$msg);
                    } 
                    $mess = $man->findBySujet($_GET["sujet_id"]);
                    if(!isset($_SESSION["views"][$_GET["sujet_id"]])){
                        SESSION::addViews($_GET["sujet_id"],1);
                    }
                    else{
                        $_SESSION["views"][$_GET["sujet_id"]]++;
                    }
                    $_POST= "";
                    
                    header('location:index.php?action=crea_mess&ok=true&sujet_id='.$_GET["sujet_id"].'&membre_id='.$_SESSION["user"]->getId());die(); 
                }
                else if(isset($_GET["ok"])){
                    $man = new SujetManager();
                    $exist = $man->findOneById($_GET["sujet_id"]);
                    // var_dump($exist);die;  
                    if($exist){
                        $sujets = $man->findAll(); 
                        $sujet = $man->findOneById($_GET["sujet_id"]);
                        $sub = new SubMessManager();
                        $sub_mess = $sub->findAll();
                        $man = new MessageManager();
                        $mess = $man->findBySujet($_GET["sujet_id"]);
                        return [
                            "view" => VIEW_DIR."crea_mess.php",
                            "data" => ["mess"=>$mess,"sujet" => $sujet,"subMess"=>$sub_mess]
                        ];
                    }
                    else{
                        $msg = "cest pas d'essayer de forcer";
                            SESSION::addFlash("error",$msg);
                            
                            return [
                                "view" => VIEW_DIR."sujet.php" ,
                                "data" => "ce compte n'existe pas"                       
                            ];

                    }
                }
                else{
                    $man = new SujetManager();
                    $sujets = $man->findAll();
                    if($_GET["close"]==1){
                        $msg = "ce sujet a été clôturé";
                        SESSION::addFlash("error",$msg);
                    }
                    return [
                        "view" => VIEW_DIR."sujet.php",
                        "data" => ["liste"=>$sujets]
                    ];
                }
            }
            else{
                
                if(!isset($_SESSION["views"][$_GET["sujet_id"]])){
                    SESSION::addViews($_GET["sujet_id"],1);
                }
                else{
                    $_SESSION["views"][$_GET["sujet_id"]]++;
                }
                $msg = "vous devez d'abord vous connecter...";
                SESSION::addFlash("error",$msg);
                $users = new MembreManager();
                $users = $users->selectUsers();
                $man = new SujetManager();
                $mess = new MessageManager();
                $mess = $mess->findAll();
                $sujets = $man->findAll(); 
                SESSION::addFlash("users",$users);
                SESSION::addFlash("liste",$sujets);
                SESSION::addFlash("mess",$mess);
                if(isset($_SESSION["liste"]) && !is_object($_SESSION["liste"]) ){
                    $i = 0;
                    // var_dump($_SESSION["mess"]);die;
                    foreach ($_SESSION["liste"] as $key => $value) {
                        if(array_key_exists($value->getId(),$_SESSION["mess"])){
                            $nb_post = $_SESSION["mess"][$value->getId()];
                        }
                        else{
                            $nb_post = 0;
                        }
                    }
                }
                // var_dump($sujets);die;
                // var_dump($_SESSION);die;
                return [
                    "view" => VIEW_DIR."sujet.php",
                    "data" => ""
                ];
            }
        }
        public function subMess(){
            $sub = new SubMessManager();
            // var_dump($_POST);die;
            $test = $sub->add($_POST);
            $sub_mess = $sub->findAll();
            // var_dump($test);die;
            $man = new MessageManager();
            $mess = $man->findBySujet($_GET["sujet_id"]);
            $man = new SujetManager();
            $sujet = $man->findOneById($_GET["sujet_id"])->getTitre();
            // var_dump($sub_mess);die;
            // $this->crea_mess($_POST);
           header('location:index.php?action=crea_mess&sujet_id='.$_GET["sujet_id"].'&membre_id='.$_SESSION["user"]->getId().'');die();
            
            // var_dump($_POST);die;
        }
        public function delete(){
            $man = new MessageManager();
            $man-> delete($_POST["message_id"]);
            // var_dump($_POST);die;
            header('location:index.php?action=sujet');
        }
        public function deleteSujet(){
            $man = new SujetManager();
            $man-> deleteSujet($_POST["sujet_id"]);
            // var_dump($_POST);die;
            header('location:index.php?action=sujet');
        }
        public function deleteUser(){
            // var_dump($_POST);die();
            $man = new MembreManager();
            $man-> deleteUser($_POST["membre_id"]);
            $users = $man->selectUsers();
            SESSION::addFlash("users",$users);
            // var_dump($_POST);die;
            header('location:index.php?action=sujet');
        }
        public function recherche(){
            $man = new SujetManager();
            $char = $_GET["nb"];
            $res = $man->recherche($char);
            $i = 0;
            $tab = [];
            if($res){
                if(is_object($res) ){
                    $titre = $res->getTitre();
                   $id = $res->getId();
                    include(VIEW_DIR."ajax.php");
                }
                else{
                    while($i < count($res)){
                        $titre = $res[$i]->getTitre();
                        $id = $res[$i]->getId();
                        
                        // var_dump($photo);
                        include(VIEW_DIR."ajax.php");
                        $i++;           
                    }
                }
            }
            // var_dump($res);
        }
        public function signal(){
            $man = new SignalementManager();
            $sign = $man->add($_POST);
            $msg = "Ce message à bien été signalé au modérateur";
           SESSION::addFlash("success",$msg);
            // var_dump($_SESSION);die;
            return [
                "view" => VIEW_DIR."sujet.php",
                "data" => ""
            ];
            // header('location:index.php?action=sujet');
        }
        public function affichSignal(){
            // var_dump($_SESSION);die;
            return [
                "view" => VIEW_DIR."affich_signal.php",
                "data" => ""
            ];
        }
        public function userMess(){
            $man = new MessageManager();
            // var_dump($_SESSION);die;
            $mess = $man->findByUserId($_SESSION["user"]->getId());
            $sub = new SubMessManager();
            $sub_mess = $sub->findAll();
            // var_dump($mess);die;
            return [
                "view" => VIEW_DIR."listeMessUser.php",
                "data" => ["mess"=>$mess,"subMess"=>$sub_mess]
            ];
        }
        public function upload($photo){
            // if(isset($_FILES) && !empty($_FILES["fileToUpload"]["name"])){
               
                $up = Upload::uploadFile("photo",$_FILES["photo"]["name"],"public/images/");
                $photo = "public/images/".$up;
                // var_dump($photo);die;
            // }
            
            return $photo;
        }
        public function findPhoto(){
            $man = new MembreManager();
            $photos = $man->findPhoto();
            SESSION::addFlash("photo",$photos);
            return [
                "view" => VIEW_DIR."gallerie.php",
                "data" => ""
            ];
        }
        public function close($id){
            $man = new SujetManager();
            $test = $man->close($id);
            $liste= $man->findAll();
            SESSION::addFlash("liste",$liste);
            // var_dump($test);die;
            return [
                "view" => VIEW_DIR."sujet.php",
                "data" => ""
            ];
        }
        public function modif($id){
            $man = new MessageManager();
            $test = $man->findOneById($id);
            $content = $test->getContent();
            // var_dump($test);die;
            // var_dump($_GET);die;
            return [
                "view" => VIEW_DIR."modif.php",
                "data" => [
                    "mess" => $test,
                    "content" => $content,
                    "sujet_id" => $_GET["sujet_id"]
                ]
            ];
        }
        public function modifContent(){
            $man = new MessageManager();
            $man->updateMess($_POST);
           var_dump($_POST);
           header("location:index.php?action=crea_mess&ok=true&sujet_id=".$_POST['sujet_id']."&membre_id=".$_POST['membre_id']);
            return [
                "view" => VIEW_DIR."sujet.php",
                "data" => ""
            ];
        }
                                       
    }
