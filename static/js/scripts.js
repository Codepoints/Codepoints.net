(function(window, $, undefined) {

function showDetails(obj) {
  var co = obj.id;
  if (obj.properties.scripts) {
    $.getJSON('/script/' + obj.properties.scripts.join(' ') +
              ' ' + obj.properties.oldscripts.join(' ')).done(function(data) {
      var d = $('<div></div>'), sc;
      $.each(obj.properties.scripts, function(i, sc) {
        if (sc in data && data[sc]) {
          d.append(
            $('<section></section>').append(
              $('<h3></h3>').append(
                $('<a></a>').text(data[sc].name)
                            .attr('href', '/search?sc='+sc)))
            .append($('<div></div>').html(data[sc].abstract)
                    .append('<p><small>Source: <a href="'+data[sc].src+'">Wikipedia</a></small></p>')));
        }
      });
      if (obj.properties.oldscripts.length) {
        d.append('<h2>Old Scripts</h2>');
      }
      $.each(obj.properties.oldscripts, function(i, sc) {
        if (sc in data && data[sc]) {
          d.append(
            $('<section></section>').append(
              $('<h3></h3>').append(
                $('<a></a>').text(data[sc].name)
                            .attr('href', '/search?sc='+sc)))
            .append($('<div></div>').html(data[sc].abstract)
                    .append('<p><small>Source: <a href="'+data[sc].src+'">Wikipedia</a></small></p>')));
        }
      });
      d.dialog({
        title: 'Scripts used in ' + obj.properties.name + ' (' + obj.id + ')',
        width: $(window).width() - 40,
        modal: true,
        resizable: false
      });
    }).fail(function() {
      showDetails({id: '___not_found___'});
    });
  } else {
    $('<div>We don’t know about '+obj.properties.name+'’s scripts, sorry!</div>').dialog({
      title: 'Nothing Found',
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
      .attr("class", function(d) { return 'sc sc_' + d.properties.scripts.join(' sc_') + ' sc_' + d.properties.oldscripts.join(' sc_')});

  feature.append("svg:title")
      .text(function(d) { return d.properties.name; });
  feature.on('click', function(e) { showDetails(e); });

  $('#athmo').remove().appendTo('#earth');
});

$('#sclist').on('mouseenter mouseleave', 'li', function(e) {
  var paths = $(),
      $this = $(this),
      cls = $this.attr('class').split(/\s+/),
      action = 'addClass', i, j;
  if (e.type === 'mouseleave') {
    action = 'removeClass';
  }
  for (i = 0, j = cls.length; i < j; i++) {
    if (cls[i].substr(0, 3) === 'sc_' && cls[i].length > 3) {
      paths = paths.add($('path.' + cls[i]));
    }
  }
  paths.each(function() {
    if (action === 'addClass') {
      this.setAttribute('class', this.getAttribute('class') + ' active');
    } else {
      this.setAttribute('class', this.getAttribute('class').replace(/\s*active\s*/, ' '));
    }
  });
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
    circle.origin(o1)
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