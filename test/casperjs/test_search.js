casper.test.begin('Check basic search results', 6, function(test) {

  casper
    .start()
    .open(home)

    .waitForResource(/\/static\/js\//)

    .wait(500)

    .thenClick('a[rel="search"]')

    .wait(400) // jQuery "fast" slideDown

    .then(function() {
      test.assertVisible('#footer_search', 'the search form slides down');
      this.fill('.searchform', {q: '&lt;'});
      this.click('.searchform button');
    })

    .waitUntilVisible('.codepoint')

    .then(function() {
      test.assertTitleMatch(/U\+003C/, 'jump directly to resulting cp');
    })

    .thenOpen(home+'search')

    .then(function() {
      this.page.customHeaders = {
          "Referer" : home+'search?',
          "Cookie": "lang=en",
      };
      this.fill('.searchform', {q: 'latin capital letter'});
      this.click('.searchform button');
      this.waitUntilVisible('.cp-list');
    })

    .then(function() {
      test.assertTitleMatch(/[0-9]+ Codepoints and [0-9]+ Blocks Found/,
        'find many codepoints w/ "q" search');
      test.assertExists('a.cp[href="/U+0041"]',
        'a relevant codepoint is found');
      test.assertExists('.up a[href*="/search?"]',
        'the link back to the results is found');
      test.assertExists('.extended.searchform input[type="text"][value="latin capital letter"]',
        'the search term is echoed to the user');
    })

  ;

  casper.run(function() {
    test.done();
  });
});
