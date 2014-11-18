// Javascript function to setup the audio player
$(function() { 
  // Setup the player to autoplay the next track
  var a = audiojs.createAll({
    trackEnded: function() {
      var next = $('ol li.playing').next();
      if (!next.length) next = $('ol li').first();
      next.addClass('playing').siblings().removeClass('playing');
      audio.load($('a', next).attr('data-src'));
      audio.play();
    }
  });
  
  //Auto load the first track
  var audio = a[0];
  first = $('ol a').attr('data-src');
  $('ol li').first().addClass('playing');
  $('.edittrack').val($('ol li.playing a').text());
  $('.deletetrack').val($('ol li.playing a').text());
  $('.downloadtrack').val($('ol li.playing a').attr('data-src'));
  audio.load(first);

  // Load in a track on click
  $('ol li').click(function(e) {
    e.preventDefault();
    $(this).addClass('playing').siblings().removeClass('playing');
    audio.load($('a', this).attr('data-src'));
    audio.play();
    $('.edittrack').val($('a', this).text());
    $('.deletetrack').val($('a', this).text());
    $('.downloadtrack').val($('a', this).attr('data-src'));
  });
});