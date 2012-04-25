(function(window, $, undefined) {

  function Question(id, text) {
    var q = this;
    q.id = id;
    q.prev = null;
    q.next = {};
    q.text = text;
    q.answers = {};
    q.selected = null;
    q.html = null;
  };

  Question.prototype = {
    addAnswer: function(id, label, next) {
      this.answers[id] = label || id;
      this.next[id] = next;
    },
    setNextForAnswer: function(id, next) {
      this.next[id] = next;
    },
    render: function() {
      if (! this.html) {
        var q = this,
            q.html = $('<fieldset id="'+q.id+'" class="question"></fieldset>');
        q.html.append('<legend>'+q.text+'</legend>');
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
      q.selected = id;
      if (q.next[id]) {
        // if there is a next question, show this
        q.html.replaceWith(q.next[id].render());
        q.next[id].prev = q;
      } else {
        // collect given answers and make request
        var answers = {}, q2 = q;
        answers[q.id] = q.selected;
        while (q2.prev) {
          q2 = q2.prev;
          answers[q2.id] = q2.selected;
        }
        console.log('?' + $.param(answers));
        //window.location.href = '?' + $.param(answers);
      }
    }
  };

  var q1 = new Question('swallow', 'What is the speed of an unladden swallow?');
  q1.addAnswer('24');
  q1.addAnswer('42');

  var q2 = new Question('hitch', 'What is the ultimate answer?');
  q2.addAnswer('25');
  q2.addAnswer('43');
  q1.setNextForAnswer('42', q2);

  $('body').append(q1.render());

})(this, jQuery);
