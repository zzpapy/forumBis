<div class='bordure'>
    <div class='head_mess'>
        <p>date :<?php echo $date?></p>
        <?php
        // var_dump($id_mess);die;
        if(isset($author_signal)){
            echo "Signalé par:".$author_signal;
        }
        ?>
        <form action="index.php?action=signal" method="POST">
            <input type="hidden" name="message_id" value="<?php echo $id_mess; ?>">
            <input type="hidden" name="membre_id" value="<?php echo $id; ?>">
            <input type="hidden" name="del" >
            <button class="button_form">Signaler</button>
        </form>
        <p>Auteur :<?php echo $pseudo?></p>
        <?php
        if(isset($_SESSION["user"])){
            if($user->getId() == $author || isset($_SESSION["admin"])){
                echo "<div><form action='index.php?action=delete&message_id=".$id_mess."' method='POST'>";
                echo ' <input type="hidden" name="membre_id" value="'.$id.'">
                <input type="hidden" name="sujet_id" value="'.$sujet_id.'">';
                echo "<button><span class=' fa-2x fas fa-times-circle'></button>";
                echo "</form></div>";           

            }
        };
        ?>
        
      
    
    </div>
    <div class="post">
        <div class="post_titre">
            <!-- <div class="photo"> -->
                <?php
                if($photo == ""){
                    $photo ="public/images/no.jpg";
                }
                else{
                    $photo = $photo;
                }
                ?>
                <img class="photo"src="<?php echo $photo?>" alt="">
            <!-- </div> -->
            <span><?php echo $content ?></span>
        </div>
                
    </div>
    <div class='subMess'>
        <h3>commentaires</h3>
        <span>ajouter un commentaire à cette publication</span> 
        <form action='index.php?action=subMess&sujet_id=<?php echo $sujet_id?>' method='POST'>
            <input class='input' type='text' name='content' value=''>
            <input type="hidden" name="membre_id" value="<?php echo $id?>">
            <input type="hidden" name="message_id" value="<?php echo $id_mess?>">
            <input type='submit'>
        </form>
    
    <div>
        <?php
           
        if($subMess){ 
            if(!is_object($subMess)){
                foreach ($result["data"]["subMess"] as $key => $value) {
                    // var_dump($value->getMessage()->getId() == $id_mess);die;
                if($value->getMessage()){
                    if($value->getMessage()->getId() == $id_mess){
    
                    // var_dump($value->getMembre());
                        echo "<div class='message_list sub'>";
                            echo "<div class='mess'>";
                            // echo $value->getMessage()->getId() == $id_mess;
                                // var_dump($value->getMembre()->getPseudo());
                                // echo "<div><p>Auteur :".$value->getMembre()->getPseudo()."</p></div>";
                                $date = new \DateTime($value->getDate());
                                $date = $date->format('d/m/Y H:i');
                                echo "<div class='content_sub'>contenu:<p class='content_mess'>".$value->getContent()."</p></div>";
                                echo "<p>date :".$date."</p>";
                                // echo "<div>id submes : ".$value->getId()."</div>";
                            echo "</div></div>";
                            echo "<div class='separator'></div>";
                        }              
                    }
                } 
            }
            else{
                if($result["data"]["subMess"]->getId() == $id_mess){
                echo "<div class='message sub'>";
                // var_dump(!is_object($subMess));die();
                // var_dump($result["data"]["subMess"]);
                            echo "<div class='head_mess'>";
                            echo $result["data"]["subMess"]->getMessage()->getId() == $id_mess;
                                echo "<div><p>Auteur :".$result["data"]["subMess"]->getMembre()->getPseudo()."</p></div>";
                                $date = new \DateTime($result["data"]["subMess"]->getDate());
                                $date = $date->format('d/m/Y H:i');
                                echo "<div class='content_sub'>contenu:<p class='content_mess'>".$result["data"]["subMess"]->getContent()."</p></div>";
                                echo "<p>date :".$date."</p>";
                                // echo "<div>id submes : ".$result["data"]["subMess"]->getId()."</div>";
                            echo "</div></div>";
                }
            }
        }
        else{
            // var_dump("toto");die;
            echo "<div>".$msg."</div>";
        }
        
        ?>
    </div>
    
</div>
</div>
           
                            