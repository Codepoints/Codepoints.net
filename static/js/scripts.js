(function(window, $, undefined) {

/**
 * show info about a single script in the opening modal dialog
 */
function renderScript(d, data) {
  return function(i, sc) {
    var n = 0, tmp = $('#sclist').find('dt.sc_'+sc);
    if (tmp.length) {
      n = parseInt(tmp.next('dd').find('.nchar').text(), 10);
      if (isNaN(n)) { n = 0; }
    }
    if (sc in data && data[sc]) {
      d.append(
        $('<section style="margin:0"></section>').append(
          $('<h3></h3>').text(data[sc].name)
            .append(' <small><a href="/search?sc[]='+sc+'">('+ n +
                            ' codepoints)</a></small>'))
        .append($('<div style="font-size: 12px"></div>').html(data[sc].abstract)
                .append('<p class="nt">Source: <a href="' + data[sc].src +
                        '">Wikipedia</a></p>')));
    }
  };
}

/**
 * load the script data and cache, if necessary
 */
var scxCache = {};
function getScripts(scx) {
  var r;
  if (! (scx in scxCache)) {
    r = $.ajax({
      url: '/script/' + scx,
      dataType: 'json'
    }).done(function (data) {
      scxCache[scx] = data;
    });
  } else {
    r = $.when(scxCache[scx]);
  }
  return r;
}

/**
 * show infos about used scripts in a country in a modal window
 */
function showDetails(obj) {
  var co = obj.id;
  if (obj.properties.scripts || obj.properties.oldscripts) {
    getScripts(obj.properties.scripts.join(' ') +
             ' ' + obj.properties.oldscripts.join(' ')
    ).done(function(data) {
      var d = $('<div></div>'), sc;
      $.each(obj.properties.scripts, renderScript(d, data));
      if (obj.properties.oldscripts.length) {
        d.append('<h2>Native, Rare and Historic Scripts</h2>');
      }
      $.each(obj.properties.oldscripts, renderScript(d, data));
      d.dialog({
        title: 'Scripts used in ' + obj.properties.name,
        buttons: {
          OK: function() { $(this).dialog('close'); }
        },
        width: $(window).width() - 40,
        modal: true,
        resizable: false
      });
    }).fail(function() {
      showDetails({id: '___not_found___'});
    });
  } else {
    $('<div>We don’t know about '+obj.properties.name+'’s scripts, sorry!</div>').dialog({
      title: 'Nothing Found for ' + obj.property.name,
      buttons: {
        Finished: function() { $(this).dialog('close'); }
      },
      width: $(window).width() - 40,
      modal: true,
      resizable: false
    });
  }
}

var feature;

var projection = d3.geo.azimuthal()
    .scale(400)
    .origin([-30,20])
    .mode("orthographic")
    .translate([400, 400]);

var circle = d3.geo.greatCircle().origin(projection.origin());

var path = d3.geo.path().projection(projection);

var svg = d3.select("#earth").on("mousedown", mousedown);

d3.json("/static/world.json", function(collection) {
  feature = svg.selectAll("path")
      .data(collection.features)
    .enter().append("svg:path")
      .attr("d", clip)
      .attr("class", function(d) {
        return 'sc sc_' + d.properties.scripts.join(' sc_') +
               ' sc_' + d.properties.oldscripts.join(' sc_');
      });

  feature.append("svg:title")
      .text(function(d) { return d.properties.name; });
  feature.on('click', function(e) { showDetails(e); });

  $('#athmo').remove().appendTo('#earth');
});

$('#sclist').accordion({
  active: false,
  autoHeight: false,
  header: '>dt',
  changestart: function(e, ui) {
    var dt = ui.newHeader, dd = dt.data('dd'), sc = dt.data('sc');
    if (! dd) {
      dd = dt.next('dd');
      dt.data('dd', dd);
      $.ajax({
        url: '/script/' + sc,
        dataType: 'json'
      }).done(function(data) {
        dd.append('<hr/>'+data[sc].abstract).append('<p class="nt">Source: <a href="' + data[sc].src + '">Wikipedia</a></p>');
      });
    }
    var paths = $(),
        cls = dt.attr('class').split(/\s+/),
        i, j;
    $.each(document.getElementsByTagName('path'), function() {
      this.setAttribute('class', this.getAttribute('class').replace(/\s*active\s*/, ' '));
    });
    for (i = 0, j = cls.length; i < j; i++) {
      if (cls[i].substr(0, 3) === 'sc_' && cls[i].length > 3) {
        paths = paths.add($('path.' + cls[i]));
      }
    }
    paths.each(function() {
      this.setAttribute('class', this.getAttribute('class') + ' active');
    });
  }
});

d3.select(window)
    .on("mousemove", mousemove)
    .on("mouseup", mouseup);

var m0,
    o0;

function mousedown() {
  m0 = [d3.event.pageX, d3.event.pageY];
  o0 = projection.origin();
  d3.event.preventDefault();
}

function mousemove() {
  if (m0) {
    var m1 = [d3.event.pageX, d3.event.pageY],
        o1 = [o0[0] + (m0[0] - m1[0]) / 8, o0[1] + (m1[1] - m0[1]) / 8];
    projection.origin(o1);
    circle.origin(o1);
    refresh();
  }
}

function mouseup() {
  if (m0) {
    mousemove();
    m0 = null;
  }
}

function refresh(duration) {
  (duration ? feature.transition().duration(duration) : feature).attr("d", clip);
}

function clip(d) {
  return path(circle.clip(d));
}

})(this, jQuery);
