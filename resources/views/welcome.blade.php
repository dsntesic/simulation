<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    
</head>
<body>
    <main class="container">
        <div class="row pt-5">
            <div class="col-4">
                <button class="btn btn-primary d-blok create-game mb-2">Create game</button>
                <a href="" class="btn btn-warning leave-game mb-2" style="display: none;">Leave game</a>
                <form id="army-form" style="display:none" class="pt-2">
                    
                    <input class='game-id-input' type='hidden' name="game_id" value="" >
                   
                    <input class="btn btn-success create-army" type="submit" value='Add army'>
                </form>
                <ul class="games-wrapper list-unstyled">
                </ul>
            </div>
            <div class="col-8 active-game" >
                <div class="game-info">
                    <p>Game number: <span></span></p>
                </div>
                <ul class="armies-wrapper list-unstyled">
                </ul>
                <div class="actions d-flex justify-content-start">
                    <button class="btn btn-danger next-attack" style="display: none;">Next attack</button>
                    <button class="btn btn-info autorun" style="display: none;">Autorun</button>
                </div>
            </div>
        </div>
    </main>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    <script>
        
        function gameTemplate(item){
            let suffix = '<button class="btn btn-sm btn-info play-game" data-id="'+item.id+'">Play game</button>';
            let template = '<li>'+item.id+'. status:in progress, armies('+item.armies.length+')'+suffix+'</li>';
            return template;
        }
                
        function refreshGames(gamesWrapper,games){
            gamesWrapper.empty();
            $.each(games, function(key, item){
                gamesWrapper.append(gameTemplate(item));
            });
        }
        
        function armyTemplate(item,game){
            let suffix = '';
            if(item.on_move && !game.finnished){
                suffix = `<button class="btn btn-sm btn-info next-attack">Attack ${item.attack_strategy}</button>`;
            }
            let template = '<li>'+item.order+' '+item.name+': '+item.current_units+'('+item.start_units+')'+suffix+'</li>';
            return template;
        }
        
        function refreshArmies(armiesWrapper,game,armies = []){
            armiesWrapper.empty();
            $.each(armies, function(key, item){
                armiesWrapper.append(armyTemplate(item,game));
            });
            if(armies.length > 0){
                if(game.in_progress && !game.finnished){
                   $('.actions button').show(); 
                }else{
                    $('.actions button').hide();
                    $('.next-attack').hide();
                    
                }
            }
        }
        
        $(document).ready(function(){
            
            var gamesWrapper = $('.games-wrapper');
            var armiesWrapper = $('.armies-wrapper');
            
            $.ajax({
                url : "{{route('lists')}}"
            }).done(function(response){ 
                 refreshGames(gamesWrapper,response.games);
            });
            
            $('.games-wrapper').on('click','button.play-game',function(){              
                $.ajax({
                    url: "{{route('get-game')}}",
                    data: {
                        'game_id':$(this).data('id')
                    }
                }).done(function(response){
                    $('#army-form').show();
                    $('.create-game').hide();
                    $('.leave-game').show();
                    $('.game-info span').text(response.game.id);
                    $('.game-id-input').val(response.game.id);
                    refreshArmies(armiesWrapper,response.game, response.armies);
                });
            });
                    
            $('button.create-game').click(function(){
                $.ajax({
                    url: "{{route('create-game')}}"
                }).done(function(response){
                    $('.game-info span').text(response.game.id);
                    let gameIdInput = $('#army-form input.game-id-input');
                    gameIdInput.val(response.game.id);
                    $('.create-game').hide();
                    $('.leave-game').show();
                    $('#army-form').show();
                    refreshArmies(armiesWrapper,response.game);
                });
            });
            
            $('.active-game').on('click','button.next-attack',function(){               
                $.ajax({
                    url: "{{route('attack')}}",
                    data: {
                        'game_id':$('.game-id-input').val()
                    }
                }).done(function(response){
                    if(response.game.finished){
                       $('#army-form').hide(); 
                       $('.games-wrapper').hide();
                    }
                    $('.game-info span').text(response.game.id);
                    refreshArmies(armiesWrapper,response.game,response.armies);
                });
            });
            
            $('.create-army').click(function(event){
                event.preventDefault(); 
                $.ajax({
                    url : "{{route('create-army')}}",
                    data: {
                        'game_id':$('.game-id-input').val()
                    }
                }).done(function(response){ 
                    $('.game-info span').text(response.game.id);
                     refreshArmies(armiesWrapper,response.game,response.armies);
                });    
            });
                       
            $('button.autorun').click(function(){
                $.ajax({
                    url: "{{route('autorun')}}",
                    data: {
                        'game_id':$('.game-id-input').val()
                    }
                }).done(function(response){
                    $('#army-form').hide();
                    $('.games-wrapper').hide();
                    refreshArmies(armiesWrapper,response.game, response.armies);
                });
            });
        });
    </script>
</body>
</html>
