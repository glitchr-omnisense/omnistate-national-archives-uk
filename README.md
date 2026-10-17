# omnistate/national-archives-uk

Documents that name a person in [Discovery](https://discovery.nationalarchives.gov.uk), the catalogue
of the UK's National Archives and of some 2,500 archives across the country, for
[omniscient/omnistate](https://github.com/glitchr-studio/omnistate).

**A catalogue of archives, not civil registration.** England and Wales' births, marriages and deaths
are held by the General Register Office, which has no public API. What Discovery answers to a name is
the description of papers that mention it - a will, a service record, a prisoner-of-war card, a
settlement examination - with their reference and who holds them. Every record is of the kind `OTHER`.

```php
$registry = new NationalArchivesUk($httpClient);
$papers = $registry->search(new CivilQuery(familyName: 'Smith', givenName: 'John', dated: Period::years(1770, 1780)));
$papers[0]->title;      // "John Smith, parish of birth Shute"
$papers[0]->archive;    // Devon Archives and Local Studies Service, 3483 A/PO 20/8
$papers[0]->url;        // its page in Discovery
```

No framework needed: the package requires `omniscient/omnistate` and `symfony/http-client`.
`$httpClient` is the HTTP client to call with - the application's, `HttpClient::create()` in
plain PHP, a `MockHttpClient` in a test.

| | |
|---|---|
| Countries | GB |
| Kinds | OTHER only (a question for births, marriages or deaths alone is not one for it) |
| Picture of the document | no |
| Persons | not structured: the name is in the title or the description |
| Access | free, no key; moderation asked: one call a second, 3,000 a day per address |

## Responsibility

This package is an independent, unofficial client: it is not affiliated with, endorsed by or
sponsored by The National Archives (UK). It reaches their service on behalf of whoever runs it, and
it does not redistribute their data beyond the short excerpts recorded as test fixtures. Its MIT
licence covers this package's code only and grants no right to the service or to its data.

Whoever installs and runs this package is responsible for complying with the Discovery API's terms
([terms and conditions for the
API](https://www.nationalarchives.gov.uk/terms-and-conditions/discovery-for-developers-about-the-application-programming-interface-api/)),
for obtaining whatever they require (an account, a key, a licence or subscription, an attribution or
a citation, a rate limit to respect), and for any use made of what it returns. The authors publish
it free of charge, make no commercial use of it, and accept no liability for how it is used (see
[LICENSE](LICENSE)).

Documentation: [docs/](docs/index.md). License: MIT since 2026-10-09; earlier versions remain published under LGPL-3.0-or-later.
