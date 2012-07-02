(function(window, $, undefined) {

  /**
   * a single question for the wizard
   */
  function Question(id, text, answers) {
    var q = this;
    q.id = id;
    q.text = text;
    q.prev = null;
    q.next = {};
    q.answers = answers || {};
    q.selected = null;
    q.html = null;
  }

  /**
   * shared methods for all wizard questions
   */
  var QuestionPrototype = {
    current: null,
    addAnswer: function(id, label, next) {
      this.answers[id] = label || id;
      this.next[id] = next;
    },
    setNextForAnswer: function(id, next) {
      this.next[id] = next;
    },
    render: function() {
      if (! this.html) {
        var q = this, i = 0;
        q.html = $('<fieldset id="'+q.id+'" class="question"></fieldset>')
                  .append('<legend>'+q.text+'</legend>')
                  .data('q', q);
        $.each(q.answers, function(id, label) {
          i += 1;
          if (id === '_number') {
            var counter = $('<input type="number"/>').val(label[1]||0),
                slider = $('<div></div>').slider({
                  min: label[1]||0,
                  max: label[2]||100,
                  change: function() {
                    counter.val($(this).slider('value'));
                  },
                  slide: function() {
                    counter.val($(this).slider('value'));
                  }
                });
            q.html.append($('<div class="number"><p></p><p><button type="button"></button></p></div>')
              .addClass('answer answer_'+i)
              .find('button')
                .html(label[0])
                .on('click tap', function() { q.select(slider.slider('value'),
                                                       q.next[id]); })
              .end().find('p:eq(0)')
                .append(counter)
                .append(slider)
              .end());
          } else if (id === '_text') {
            var txt = $('<input type="text"/>').on('keypress', function(e) {
              if (e.which === 13) {
                $(this).next().click();
                return false;
              }
            });
            q.html.append($('<p class="text"><button type="button"></button></p>')
              .find('button')
                .html(label)
                .before(txt)
                .on('click tap', function() { q.select(txt.val(), q.next[id]); })
              .end().addClass('answer answer_'+i));
          } else {
            q.html.append($('<p><button type="button"></button></p>')
              .find('button')
                .html(label)
                .on('click tap', function() { q.select(id, q.next[id]); })
              .end().addClass('answer answer_'+i));
          }
        });
      }
      return this.html;
    },
    select: function(id, next) {
      var q = this;
      q.selected = id;
      q.html.trigger('question.answered', q);
      if (next) {
        // if there is a next question, show this
        q.html.fadeOut('fast', function() {
          var next_html = next.render();
          QuestionPrototype.current = next;
          next_html.trigger('question.next', {prev:q, next:next});
          next_html.hide().insertAfter(q.html).fadeIn('fast');
          q.html.detach();
        });
        next.prev = q;
      } else {
        finishAsking(q);
      }
    }
  };
  Question.prototype = QuestionPrototype;

  /**
   * finish asking, either by user request or when no next question exists
   */
  function finishAsking(q) {
    // collect given answers and make request
    q.html.trigger('question.finish', q);
    var answers = {'_wizard': 1}, q2 = q, i = 0,
        html = $('<fieldset id="wizard_finish">' +
                   '<legend>Finished!</legend>' +
                 '</fieldset>');
    if (q.selected !== null) {
      answers[q.id] = q.selected;
      i += 1;
    }
    while (q2.prev) {
      q2 = q2.prev;
      answers[q2.id] = q2.selected;
      i += 1;
    }
    QuestionPrototype.container.addClass('finished');
    html.append($('<p></p>').text('Please wait a second, we’re making those '+
                                   i+' answers productive.').prepend('<img src="/static/images/ajax.gif" alt="" width="16" height="16"/> '));
    $('#wizard_now').fadeOut('fast');
    q.html.fadeOut('fast', function() {
      html.hide().insertAfter(q.html).fadeIn('fast');
      q.html.detach();
      window.location.href = '?' + $.param(answers);
    });
  }

  /**
   * initialize the markup
   */
  function prepareContainer(container, q1) {
    var ol = $('<ol class="wizcount"/>');
    QuestionPrototype.current = q1;
    QuestionPrototype.container = container;
    container.empty()
        .append(ol)
        .append(q1.render())
        .one('question.answered', function() {
          $(this).after($('<p class="buttonset" id="wizard_now">' +
              '<button type="button">Enough questions! Search now.</button>' +
            '</p>').find('button').on('click', function() {
            finishAsking(QuestionPrototype.current);
          }).end().hide().slideDown());
        })
        .on('question.answered', function(e, q) {
          ol.append(
              $('<li></li>').attr('title', q.text).tooltip()
                  .html(q.answers[q.selected])
                  .hide().slideDown().on('click', function() {
                    if (container.is('.finished')) {
                      return false; // don't change the set, if there is
                                    // already a request going
                    }
                    var cur = QuestionPrototype.current, li = $(this);
                    li.add(li.nextAll('li')).remove();
                    QuestionPrototype.current = q;
                    q.render().hide().insertAfter(cur.html).fadeIn('fast');
                    cur.html.detach();
                  })
          );
        });
  }

  var q_swallow = new Question('swallow',
    'What’s the airspeed velocity of an unladen swallow?', {
      1: 'An African, or',
      2: 'A European Swallow?'
  });

  var q_region = new Question('region',
    'Where does the character appear usually?', {
      'Africa': 'Africa <small>(without Arabic)</small>',
      'America': 'America <small>(originally, <i>e. g.</i> Cherokee, not Latin)</small>',
      'Europe': 'Europe <small>(Latin, Cyrillic, …)</small>',
      'Middle_East': 'Middle East',
      'Central_Asia': 'Central Asia',
      'East_Asia': 'East Asia <small>(Chinese, Korean, Japanese, …)</small>',
      'South_Asia': 'South Asia <small>(Indian)</small>',
      'Southeast Asia': 'Southeast Asia <small>(Thai, Khmer, …)</small>',
      'Philippines': 'Philippines, Indonesia, Oceania',
      'n': 'Nowhere specific',
      '': 'I don’t know'
  });

  var q_number = new Question('number',
    'Is it a number of any kind?', {
      1: 'Yes',
      0: 'No',
      '': 'I don’t know'
  });

  var q_case = new Question('case',
    'Has the character a case (upper, lower, title)?', {
      l: 'Yes, it’s lowercase',
      u: 'Yes, it’s uppercase',
      t: 'Yes, it’s titlecase',
      y: 'Yes, but I don’t know the case',
      n: 'No, it’s uncased',
      '': 'I don’t know'
  });

  var q_symbol = new Question('symbol',
    'Is the character some kind of symbol or dingbat?', {
      s: 'Yes <small>(It isn’t part of usually written text)</small>',
      c: 'No <small>(But it is some kind of control character)</small>',
      t: 'No <small>(It may appear in text, like letters or punctuation)</small>',
      '': 'I don’t know'
  });

  var q_punc = new Question('punctuation',
    'Is the character some kind of punctuation?', {
      1: 'Yes',
      0: 'No',
      '': 'I don’t know'
  });

  var q_incomplete = new Question('incomplete',
    'Is the character incomplete on its own, like a diacritic sign?', {
      1: 'Yes <small>(It’s usually found together with another character)</small>',
      0: 'No <small>(It stands on its own)</small>',
      '': 'I don’t know'
  });

  var q_composed = new Question('composed',
    'Is the character composed of two others?', {
      1: 'Yes <small>(It is based on two or more other characters)</small>',
      2: 'Sort of <small>(It’s got some quiggly lines or dots, like “Ä” or “ٷ”)</small>',
      0: 'No <small>(It is a genuine character)</small>',
      '': 'I don’t know'
  });

  var q_confuse = new Question('confuse',
    'Off the top of your head, can the character be confused with another one?', {
      1: 'Yes <small>(Like latin “A” and greek “Α”, alpha)</small>',
      '': 'No <small>(I have no such pair in mind)</small>'
  });

  var q_archaic = new Question('archaic',
    'Is it an archaic character or is it in use today?', {
      1: 'Yep, noone would use that anymore!',
      0: 'Nah, seen it yesterday in the newspaper',
      '': 'I don’t know'
  });

  var q_strokes = new Question('strokes',
    'Do you know the number of strokes the character has?', {
      _number: ['This much', 1, 64],
      '': 'Nope, never counted them'
  });

  var q_def = new Question('def',
    'Do you happen to know the meaning of the character?', {
      _text: 'This is (part of) what I’m looking for',
      '': 'I don’t speak that language'
  });


  q_region.setNextForAnswer('Africa', q_number);
  q_region.setNextForAnswer('America', q_number);
  q_region.setNextForAnswer('Europe', q_number);
  q_region.setNextForAnswer('Middle_East', q_number);
  q_region.setNextForAnswer('Central_Asia', q_number);
  q_region.setNextForAnswer('South_Asia', q_number);
  q_region.setNextForAnswer('Southeast_Asia', q_number);
  q_region.setNextForAnswer('Philippines', q_number);
  q_region.setNextForAnswer('n', q_symbol);
  q_region.setNextForAnswer('', q_symbol);

  q_region.setNextForAnswer('East_Asia', q_def);
  q_def.setNextForAnswer('_text', q_strokes);
  q_def.setNextForAnswer('', q_strokes);

  q_strokes.setNextForAnswer('_number', q_composed);
  q_strokes.setNextForAnswer('', q_composed);

  q_number.setNextForAnswer(0, q_symbol);
  q_number.setNextForAnswer('', q_symbol);

  q_symbol.setNextForAnswer('t', q_punc);
  q_symbol.setNextForAnswer('', q_punc);

  q_punc.setNextForAnswer(1, q_archaic);
  q_punc.setNextForAnswer(0, q_case);
  q_punc.setNextForAnswer('', q_case);

  q_case.setNextForAnswer('l', q_composed);
  q_case.setNextForAnswer('u', q_composed);
  q_case.setNextForAnswer('t', q_composed);
  q_case.setNextForAnswer('y', q_composed);
  q_case.setNextForAnswer('n', q_composed);
  q_case.setNextForAnswer('', q_composed);

  q_composed.setNextForAnswer(1, q_archaic);
  q_composed.setNextForAnswer(2, q_archaic);
  q_composed.setNextForAnswer(0, q_incomplete);
  q_composed.setNextForAnswer('', q_incomplete);

  q_incomplete.setNextForAnswer(1, q_archaic);
  q_incomplete.setNextForAnswer(0, q_archaic);
  q_incomplete.setNextForAnswer('', q_archaic);

  q_archaic.setNextForAnswer(1, q_confuse);
  q_archaic.setNextForAnswer(0, q_confuse);
  q_archaic.setNextForAnswer('', q_confuse);

  prepareContainer($('#wizard_container'), q_region);

})(this, jQuery);
