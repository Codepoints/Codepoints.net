(function(window, $, undefined) {

var scripts = {
AD:['Latn'],
AE:['Arab'],
AF:['Arab'],
AL:['Latn'],
AM:['Armn'],
AN:['Latn'],
AO:['Latn'],
AR:['Latn'],
AS:['Latn'],
AT:['Latn'],
AW:['Latn'],
AX:['Latn'],
AZ:['Latn'],
BA:['Cyrl','Latn'],
BD:['Beng'],
BE:['Latn'],
BF:['Latn'],
BG:['Cyrl'],
BH:['Arab'],
BI:['Latn'],
BJ:['Latn'],
BL:['Latn'],
BN:['Latn'],
BO:['Latn'],
BR:['Latn'],
BT:['Tibt'],
BY:['Cyrl'],
CD:['Latn'],
CF:['Latn'],
CG:['Latn'],
CH:['Latn'],
CI:['Latn'],
CL:['Latn'],
CM:['Latn'],
CN:['Arab','Hans','Latn','Mong','Tibt','Yiii'],
CO:['Latn'],
CP:['Latn'],
CR:['Latn'],
CU:['Latn'],
CV:['Latn'],
CY:['Grek','Latn'],
CZ:['Latn'],
DE:['Latn'],
DJ:['Latn'],
DK:['Latn'],
DO:['Latn'],
DZ:['Arab','Latn'],
EA:['Latn'],
EC:['Latn'],
EE:['Latn'],
EG:['Arab'],
EH:['Arab'],
ER:['Ethi','Latn'],
ES:['Latn'],
ET:['Ethi','Latn'],
FI:['Latn'],
FJ:['Latn'],
FM:['Latn'],
FO:['Latn'],
FR:['Latn'],
GA:['Latn'],
GB:['Latn'],
GE:['Cyrl','Geor'],
GF:['Latn'],
GH:['Latn'],
GL:['Latn'],
GN:['Latn'],
GP:['Latn'],
GQ:['Latn'],
GR:['Grek'],
GT:['Latn'],
GU:['Latn'],
GW:['Latn'],
HK:['Hant'],
HN:['Latn'],
HR:['Latn'],
HT:['Latn'],
HU:['Latn'],
IC:['Latn'],
ID:['Latn'],
IE:['Latn'],
IL:['Hebr'],
IN:['Arab','Beng','Deva','Gujr','Guru','Knda','Latn','Mlym','Orya','Taml','Telu'],
IQ:['Arab'],
IR:['Arab'],
IS:['Latn'],
IT:['Latn'],
JO:['Arab'],
JP:['Jpan'],
KE:['Latn'],
KG:['Cyrl'],
KH:['Khmr'],
KI:['Latn'],
KM:['Arab','Latn'],
KP:['Kore'],
KR:['Kore'],
KW:['Arab'],
KZ:['Cyrl'],
LA:['Laoo'],
LB:['Arab'],
LI:['Latn'],
LK:['Sinh'],
LR:['Latn','Vaii'],
LS:['Latn'],
LT:['Latn'],
LU:['Latn'],
LV:['Latn'],
LY:['Arab'],
MA:['Arab','Latn','Tfng'],
MC:['Latn'],
MD:['Latn'],
ME:['Latn'],
MF:['Latn'],
MG:['Latn'],
MH:['Latn'],
MK:['Cyrl','Latn'],
ML:['Latn'],
MM:['Mymr'],
MN:['Cyrl'],
MO:['Hant'],
MQ:['Latn'],
MR:['Arab'],
MT:['Latn'],
MU:['Latn'],
MV:['Thaa'],
MW:['Latn'],
MX:['Latn'],
MY:['Latn'],
MZ:['Latn'],
NA:['Latn'],
NC:['Latn'],
NE:['Latn'],
NG:['Arab','Latn'],
NI:['Latn'],
NL:['Latn'],
NO:['Latn'],
NP:['Deva'],
NR:['Latn'],
NU:['Latn'],
NZ:['Latn'],
OM:['Arab'],
PA:['Latn'],
PE:['Latn'],
PF:['Latn'],
PG:['Latn'],
PH:['Latn'],
PK:['Arab'],
PL:['Latn'],
PM:['Latn'],
PR:['Latn'],
PS:['Arab'],
PT:['Latn'],
PW:['Latn'],
PY:['Latn'],
QA:['Arab'],
RE:['Latn'],
RO:['Latn'],
RS:['Cyrl','Latn'],
RU:['Cyrl'],
RW:['Latn'],
SA:['Arab'],
SC:['Latn'],
SD:['Arab','Latn'],
SE:['Latn'],
SI:['Latn'],
SJ:['Latn'],
SK:['Latn'],
SM:['Latn'],
SN:['Latn'],
SO:['Latn'],
SR:['Latn'],
SS:['Latn'],
ST:['Latn'],
SV:['Latn'],
SY:['Arab','Latn'],
TD:['Latn'],
TG:['Latn'],
TH:['Thai'],
TJ:['Cyrl'],
TK:['Latn'],
TL:['Latn'],
TM:['Latn'],
TN:['Arab','Latn'],
TO:['Latn'],
TR:['Latn'],
TV:['Latn'],
TW:['Hant','Latn'],
TZ:['Latn'],
UA:['Cyrl'],
UG:['Latn'],
US:['Cher','Latn'],
UY:['Latn'],
UZ:['Cyrl'],
VA:['Latn'],
VE:['Latn'],
VN:['Latn'],
VU:['Latn'],
WF:['Latn'],
WS:['Latn'],
YE:['Arab'],
YT:['Latn'],
ZA:['Latn'],
ZM:['Latn'],
ZW:['Latn']
};

function showDetails(obj) {
  var co = obj.id.substr(0, 2);
  if (co in scripts) {
    $.getJSON('/script/'+scripts[co].join(' '), function(data) {
      var d = $('<div></div>'), sc;
      $.each(scripts[co], function(i, sc) {
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
        title: obj.properties.name,
        width: $(window).width() - 40,
        modal: true,
        resizable: false
      }).on('click', function() {
        $(this).dialog('close');
      });
    });
  }
}

var feature;

var projection = d3.geo.azimuthal()
    .scale(380)
    .origin([-30,20])
    .mode("orthographic")
    .translate([640, 400]);

var circle = d3.geo.greatCircle()
    .origin(projection.origin());

var path = d3.geo.path()
    .projection(projection);

var svg = d3.select("#earth")
    .attr("width", 1280)
    .attr("height", 800)
    .on("mousedown", mousedown);

d3.json("/static/world-countries.json", function(collection) {
  feature = svg.selectAll("path")
      .data(collection.features)
    .enter().append("svg:path")
      .attr("d", clip);

  feature.append("svg:title")
      .text(function(d) { return d.properties.name; });
  feature.on('click', function(e) { console.log(e); showDetails(e); });

//  svg.append('svg:circle')
//      .attr('cx', 640)
//      .attr('cy', 400)
//      .attr('r', 380)
//      .attr('style', 'pointer-events: none; fill: url(#reflect)');
  $('#athmo').remove().appendTo('#earth');
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
