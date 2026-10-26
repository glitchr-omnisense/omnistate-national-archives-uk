# omnistate/national-archives-uk

Documents that name a person in [Discovery](https://discovery.nationalarchives.gov.uk), the
catalogue of the UK's National Archives and of some 2,500 archives across the country, for
[omnisense/omnistate](https://github.com/glitchr-omnisense/omnistate): wills, service records,
prisoner-of-war cards, settlement examinations, parish papers, described piece by piece.

> [!IMPORTANT]
> A catalogue of archives, not civil registration. England and Wales' births, marriages and deaths
> are held by the General Register Office, which has no public API. What Discovery answers to a
> name is the description of papers that mention it, with their reference and who holds them:
> every record is of the kind `OTHER`, with no structured person and no picture.

| Operation | Request |
|---|---|
| `search($query)` | `GET https://discovery.nationalarchives.gov.uk/API/search/records?sps.searchQuery=…&sps.dateFrom=…&sps.dateTo=…&sps.resultsPageSize=…&sps.page=…` |
| `find($id)` | `GET https://discovery.nationalarchives.gov.uk/API/records/v1/details/{id}` |

The base address is the constant `NationalArchivesUk::URL` (or the `url` argument); a record's page
is `NationalArchivesUk::PAGE` + its id (`https://discovery.nationalarchives.gov.uk/details/r/…`).

## Installation

```sh
composer require omnisense/omnistate omnistate/national-archives-uk
```

Requires PHP 8.2, `omnisense/omnistate` (1.x) and `symfony/http-client` (6.4, 7 or 8). In a
Symfony application, `OmnistateBundle` registers it when it is installed (tag
`omnistate.civil_registry`); nothing to configure.

## Credentials

None: the API is open, without an account or a key. The API asks for moderation: one call a
second, at most 3,000 a day per address. The class does not pace its calls: the facade's cache
spares them.

## Options

| Argument | Type | Default | |
|---|---|---|---|
| `http` | `HttpClientInterface` | required | the client to call with |
| `timeout` | float | `10` | seconds Discovery has to answer |
| `url` | string | `https://discovery.nationalarchives.gov.uk/API` | the API's base address |

## Capabilities

| | |
|---|---|
| Contract | `CivilRegistryInterface` (`name()`: `national-archives-uk`) |
| `countries()` | `['GB']` |
| `kinds()` | `[CivilRecordKind::OTHER]` only |
| `period()` | `null` |
| `hasImages()` | `false` |
| `search()`, `find()` | yes |

`supports()` is true for a query with a name, for GB or no country, whose kinds include `OTHER` or
are empty: a question for births, marriages or deaths alone is not one for it.

## Behaviour

**A search.** From the `CivilQuery`:

| Query | Parameter |
|---|---|
| `givenName`, `familyName`, `place` | `sps.searchQuery` (full text) |
| `dated` | `sps.dateFrom`, `sps.dateTo` (`YYYY-MM-DD`, the earliest and the latest day of the period): the dates the document covers |
| `limit`, `page` | `sps.resultsPageSize`, `sps.page` (from 0, sent from the second page) |

`born`, `died`, `sex` and `country` are not searched on.

**A record.** `find()` takes an id of 2 to 60 letters, digits, `_` or `-` (anything else is `null`
without a request).

**What comes back.** A `CivilRecord` of kind `OTHER`, from a search line or the details (the two
do not name their fields alike):

- `title`: the piece's title, else its description (tags stripped, entities decoded);
- `description`: its place in the catalogue (`context`), its description when it is not the
  title, its covering dates, joined by ` — `;
- `date`: when the piece is of one year (the day when it is of one day); `period`: the span it
  covers (`numStartDate`/`numEndDate`, else `coveringFromDate`/`coveringToDate`), whole years
  told as years (`1939/1945`);
- `archive`: the holder (`heldBy`, else `The National Archives`), the reference
  (`reference`, else `citableReference`), the record's page;
- `url`: its page in Discovery; `persons` and `images` empty; `raw` the line or the details.

**Caching.** None of its own: the facade's cache keeps its answers.

## Example

```php
<?php

require __DIR__.'/vendor/autoload.php';

use Omnistate\Model\CivilQuery;
use Omnistate\Model\CivilRecordKind;
use Omnistate\Model\Period;
use Omnistate\NationalArchivesUk\NationalArchivesUk;
use Symfony\Component\HttpClient\HttpClient;

$registry = new NationalArchivesUk(HttpClient::create());

var_dump($registry->supports(new CivilQuery(familyName: 'Smith', kinds: [CivilRecordKind::BIRTH])));
$papers = $registry->search(new CivilQuery(familyName: 'Smith', givenName: 'John', dated: Period::years(1770, 1780), limit: 3));
foreach ($papers as $paper) {
    printf("%s  %s\n  %s | %s | date %s, period %s\n  %s\n", $paper->identifier, $paper->title, $paper->archive, $paper->description, $paper->date ?? '-', $paper->period ?? '-', $paper->url);
}

$record = $registry->find($papers[0]->identifier);
printf("\nfound again: %s, %s\n", $record->identifier, $record->title);
var_dump($registry->find('nothing-here-123'));
```

```
bool(false)
2bf7ed32-739e-4cc8-b63d-2702b680aa96  John Smith
  Devon Archives and Local Studies Service (South West Heritage Trust), 1255 A/PO 31 | Rewe Parish.OVERSEERS.Settlement.Settlement Examinations. — Now residing in Thorverton, husbandman. Born in Rewe, bound apprentice there to Mr Kingdom, with whom he lived until he was 21 yrs. Then went to Plymouth with his master's consent. Lived as an ostler with one herd for seven weeks. Afterwards he went to sea, returning in 1761. He went to Thorverton, where he married, lived there ever since — 1770 | date 1770, period 1770
  https://discovery.nationalarchives.gov.uk/details/r/2bf7ed32-739e-4cc8-b63d-2702b680aa96
C7378568  Smith, John
  The National Archives, Kew, ASSI 45/30/1/108-113 | Assizes: Northern and North-Eastern Circuits: Criminal Depositions and Case Papers. Depositions and case papers. (Described at item level). — Smith, John. — 1771 | date 1771, period 1771
  https://discovery.nationalarchives.gov.uk/details/r/C7378568
cb2b7924-a163-4b2d-bd26-8070b42b784f  John Smith.
  Walsall Archives, 277/152/4 | Walsall Town Chest - Part Two.Admission of Capital Burgesses.Memorandam of admission of Capital Burgesses to the Corporation. — 20 Sept. 1773 | date 1773, period 1773
  https://discovery.nationalarchives.gov.uk/details/r/cb2b7924-a163-4b2d-bd26-8070b42b784f

found again: 2bf7ed32-739e-4cc8-b63d-2702b680aa96, John Smith
NULL
```

(run on 2026-10-25 in omnistate's Docker harness, against Discovery)

Through the facade, with the other civil registers: `$omnistate->civilRecords($query)`; a question
with `country: 'GB'` reaches this register only.

## Errors

- HTTP 429: `UnavailableException` (`Discovery answered 429.`), `retryAfter` from `Retry-After`
  (at least 1), else 60 s.
- HTTP 5xx: `UnavailableException`, no `retryAfter`.
- HTTP 401 or 403: `UnavailableException` (`Discovery refused the call (403).`).
- No answer, a body that is not JSON: `UnavailableException` (`Discovery did not answer: ...`).
- HTTP 204 or 404 on the details, or an empty body: no such record, `null`.
- Another 4xx: `OmnistateException` (`Discovery refused the question (400).`).
- An empty body on a search: `[]`.

## Verified, and not

Tested offline: `Tests/NationalArchivesUkTest.php` (a catalogue, not civil registration; documents
that name someone; the details of a record; no such record, 204; nothing found; down is not
nobody) and `Tests/NationalArchivesUkContractTest.php`, omnistate's conformance suite.

Against the real service on 2026-10-04: a search by name, with dates, with no result; the details
of a record of the National Archives and of a local archive; an unknown record (204); the fixtures
in `Tests/Fixtures/` are its answers. Run again on 2026-10-25 (the example above). Paging
(`sps.page`) was not tried.

## Responsibility

An independent, unofficial client, not affiliated with The National Archives (UK). Whoever runs it
complies with the Discovery API's terms ([terms and conditions for the
API](https://www.nationalarchives.gov.uk/terms-and-conditions/discovery-for-developers-about-the-application-programming-interface-api/)):
see the README's Responsibility section.
