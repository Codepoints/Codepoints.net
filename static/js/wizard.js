(function(window, $, undefined) {

  /**
   * a single question for the wizard
   */
  function Question(id, text) {
    var q = this;
    q.id = id;
    q.text = text;
    q.prev = null;
    q.next = {};
    q.answers = {};
    q.selected = null;
    q.html = null;
  };

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
        var q = this;
        q.html = $('<fieldset id="'+q.id+'" class="question"></fieldset>')
                  .append('<legend>'+q.text+'</legend>')
                  .data('q', q);
        $.each(q.answers, function(id, label) {
          q.html.append($('<p><button type="button"></button></p>')
            .find('button')
              .text(label)
              .on('click tap', function() { q.select(id); })
            .end());
        });
      }
      return this.html;
    },
    select: function(id) {
      var q = this;
      q.html.trigger('question.answered');
      q.selected = id;
      if (q.next[id]) {
        // if there is a next question, show this
        q.html.fadeOut('fast', function() {
          var next_html = q.next[id].render();
          QuestionPrototype.current = q.next[id];
          next_html.trigger('question.next');
          next_html.hide().insertAfter(q.html).fadeIn('fast');
          q.html.remove();
        });
        q.next[id].prev = q;
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
    var answers = {}, q2 = q, i = 0,
        html = $('<fieldset id="wizard_finish"><legend>Finished!</legend></fieldset>');
    if (q.selected !== null) {
      answers[q.id] = q.selected;
      i += 1;
    }
    while (q2.prev) {
      q2 = q2.prev;
      answers[q2.id] = q2.selected;
      i += 1;
    }
    html.append($('<p></p>').text('Let’s make those '+i+' questions productive.'));
    $('#wizard_now').fadeOut('fast');
    q.html.fadeOut('fast', function() {
      html.hide().insertAfter(q.html).fadeIn('fast');
      q.html.remove();
      console.log('?' + $.param(answers));
      //window.location.href = '?' + $.param(answers);
    });
  }

  /**
   *
   */
  function prepareContainer(container, q1) {
    QuestionPrototype.current = q1;
    container.empty().append(q1.render()).one('question.answered',
      function() {
        $(this).after($('<p class="buttonset" id="wizard_now">' +
            '<button type="button">Enough questions! Search now.</button>' +
          '</p>').find('button').on('click', function() {
          finishAsking(QuestionPrototype.current);
        }).end().hide().slideDown());
      });
  }

  var q1 = new Question('swallow', 'What is the speed of an unladden swallow?');
  q1.addAnswer('24');
  q1.addAnswer('42');
  q1.addAnswer('64');

  var q2 = new Question('hitch', 'What is the ultimate answer?');
  q2.addAnswer('25');
  q2.addAnswer('43');
  q1.setNextForAnswer('42', q2);

  var q3 = new Question('foobar', 'Foo or Bar?');
  q3.addAnswer('Foo');
  q3.addAnswer('Bar');
  q1.setNextForAnswer('64', q3);
  q2.setNextForAnswer('43', q3);

  prepareContainer($('#wizard_container'), q1);

})(this, jQuery);
