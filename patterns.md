# wp_commenter_csv_export reviewer patterns

Snapshot: `bef6d64ed212fd569643cc87318d1b443aee92fe`. [Repository](https://github.com/seerinteractive/wp_commenter_csv_export).

These reviewer patterns apply to the pinned snapshot. Accepted items passed independent refutation and deterministic evidence checks at the pinned snapshot. Quotes verify source presence; they do not prove every interpretation or current production behavior.

Coverage: 6 extracted business rules; 5 accepted, 0 uncertain, 1 rejected, 0 pending audit. 2 extracted dependencies; 1 accepted, 0 uncertain, 1 rejected, 0 pending audit.

Scanned branch: `master`. GitHub default branch: `master`.

## Accepted business rules

### approved-comments-only

In wp_commenter_csv_export, CSV contacts and the authority-update URL list must come only from WordPress comments with comment_approved='1'. Unapproved comments must not contribute contacts or commented-post links.

**Why:** Reason not stated in the repo.

**Applies to:** commenters-csv.php; get-seomoz.php

**Evidence:**

- [wp_commenter_csv_export/commenters-csv.php:106](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/commenters-csv.php#L106)

  ```text
  $data= $wpdb->get_results("SELECT comment_ID, comment_author, comment_author_email, comment_author_url, comment_post_ID
  ```
- [wp_commenter_csv_export/commenters-csv.php:107](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/commenters-csv.php#L107)

  ```text
  FROM $wpdb->comments WHERE comment_approved='1'
  ```
- [wp_commenter_csv_export/commenters-csv.php:127](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/commenters-csv.php#L127)

  ```text
  $post_link[$res->comment_post_ID] = get_permalink($res->comment_post_ID);
  ```
- [wp_commenter_csv_export/commenters-csv.php:133](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/commenters-csv.php#L133)

  ```text
  $commented_by[$res->comment_author_email][]= $post_link[$res->comment_post_ID];
  ```
- [wp_commenter_csv_export/get-seomoz.php:21](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/get-seomoz.php#L21)

  ```text
  WHERE comment_author_url!='' AND comment_approved='1' GROUP BY comment_author_url
  ```

### one-contact-per-email

In wp_commenter_csv_export, CSV contacts must be deduplicated by comment_author_email rather than name or URL. Each contact's Commented Posts field must collect distinct post permalinks from approved comments with that email.

**Why:** Reason not stated in the repo.

**Applies to:** commenters-csv.php

**Evidence:**

- [wp_commenter_csv_export/commenters-csv.php:107](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/commenters-csv.php#L107)

  ```text
  FROM $wpdb->comments WHERE comment_approved='1'
  ```
- [wp_commenter_csv_export/commenters-csv.php:127](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/commenters-csv.php#L127)

  ```text
  $post_link[$res->comment_post_ID] = get_permalink($res->comment_post_ID);
  ```
- [wp_commenter_csv_export/commenters-csv.php:131](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/commenters-csv.php#L131)

  ```text
  !in_array($post_link[$res->comment_post_ID], $commented_by[$res->comment_author_email] ) )
  ```
- [wp_commenter_csv_export/commenters-csv.php:133](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/commenters-csv.php#L133)

  ```text
  $commented_by[$res->comment_author_email][]= $post_link[$res->comment_post_ID];
  ```
- [wp_commenter_csv_export/commenters-csv.php:142](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/commenters-csv.php#L142)

  ```text
  if ( !in_array($res->comment_author_email, $email_is_in_csv) ) {
  ```
- [wp_commenter_csv_export/commenters-csv.php:145](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/commenters-csv.php#L145)

  ```text
  implode($commented_by[$res->comment_author_email], ' ')
  ```
- [wp_commenter_csv_export/commenters-csv.php:149](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/commenters-csv.php#L149)

  ```text
  $email_is_in_csv[]= $res->comment_author_email;
  ```

### privileged-export-and-authority-actions

In wp_commenter_csv_export, exporting commenter contacts and invoking ajax.php to refresh or reset SEOmoz scores must require the WordPress manage_options capability.

**Why:** Reason not stated in the repo.

**Applies to:** commenters-csv.php; ajax.php

**Evidence:**

- [wp_commenter_csv_export/commenters-csv.php:103](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/commenters-csv.php#L103)

  ```text
  if ( !current_user_can('manage_options') )
  ```
- [wp_commenter_csv_export/commenters-csv.php:104](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/commenters-csv.php#L104)

  ```text
  wp_die('You are not allowed to export comments data.');
  ```
- [wp_commenter_csv_export/ajax.php:4](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/ajax.php#L4)

  ```text
  if ( !current_user_can('manage_options') ) {
  ```
- [wp_commenter_csv_export/ajax.php:5](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/ajax.php#L5)

  ```text
  die('commcsv -- No access for current WP user.');
  ```

### optional-authority-enrichment

In wp_commenter_csv_export, CSV contact export must remain available without SEOmoz credentials. Missing cached upa and pda values must produce empty Page Authority and Domain Authority fields.

**Why:** The README describes authority metrics as optional and useful for identifying influential commenters for outreach.

**Applies to:** commenters-csv.php; README.md

**Evidence:**

- [wp_commenter_csv_export/README.md:6](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/README.md#L6)

  ```text
  Optionally, you can get the SEOmoz page authority and domain authority metrics for a commenter's URL if they has filled that out in your blog.
  ```
- [wp_commenter_csv_export/README.md:6](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/README.md#L6)

  ```text
  This is usful if you're looking to reach out to those people and want to figure out who is the most influential.
  ```
- [wp_commenter_csv_export/commenters-csv.php:59](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/commenters-csv.php#L59)

  ```text
  You can still export your commenter's info
  ```
- [wp_commenter_csv_export/commenters-csv.php:140](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/commenters-csv.php#L140)

  ```text
  \"Page Authority\"$sep\"Domain Authority\"
  ```
- [wp_commenter_csv_export/commenters-csv.php:146](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/commenters-csv.php#L146)

  ```text
  ( isset($upa[$res->comment_ID])?$upa[$res->comment_ID]:'' )
  ```
- [wp_commenter_csv_export/commenters-csv.php:147](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/commenters-csv.php#L147)

  ```text
  ( isset($pda[$res->comment_ID])?$pda[$res->comment_ID]:'' )
  ```

### locale-dependent-csv-separator

In wp_commenter_csv_export, CSV generation must use commenter_exporter.separator and default to a comma when it is empty. Exporter settings must retain comma and semicolon choices for locale-dependent CSV imports.

**Why:** The settings page explicitly states that the CSV separator character depends on the user's OS locale.

**Applies to:** commenters-csv.php; seomoz-id.php

**Evidence:**

- [wp_commenter_csv_export/commenters-csv.php:96](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/commenters-csv.php#L96)

  ```text
  $exporter= get_option('commenter_exporter');
  ```
- [wp_commenter_csv_export/commenters-csv.php:98](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/commenters-csv.php#L98)

  ```text
  if ( empty($exporter['separator']) )
  ```
- [wp_commenter_csv_export/commenters-csv.php:99](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/commenters-csv.php#L99)

  ```text
  $sep= ',';
  ```
- [wp_commenter_csv_export/commenters-csv.php:101](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/commenters-csv.php#L101)

  ```text
  $sep= $exporter['separator'];
  ```
- [wp_commenter_csv_export/seomoz-id.php:37](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/seomoz-id.php#L37)

  ```text
  <b>,</b> (comma)
  ```
- [wp_commenter_csv_export/seomoz-id.php:39](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/seomoz-id.php#L39)

  ```text
  <b>;</b> (semicolon)
  ```
- [wp_commenter_csv_export/seomoz-id.php:40](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/seomoz-id.php#L40)

  ```text
  (CSV separator character depends on your OS locale)
  ```

## Accepted dependencies

### SEOmoz (external system)

The checked-in legacy SEOmoz integration in wp_commenter_csv_export requests the Linkscape URL Metrics endpoint with Cols=103079215104 and caches score data in WordPress comment metadata under seomoz. Its authority UI and CSV export consume upa as Page Authority and pda as Domain Authority.

**Keep in sync:** Changes to the request, response parser, or cache format must preserve compatibility with the upa/Page Authority and pda/Domain Authority readers in get-seomoz.php and commenters-csv.php, or update those readers together.

**Why reviewers need it:** CSV export reads cached metrics rather than fetching SEOmoz data, so integration changes must account for both the authority UI and CSV readers.

**Evidence:**

- [wp_commenter_csv_export/ajax.php:32](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/ajax.php#L32)

  ```text
  $answer= $curl->request( "http://lsapi.seomoz.com/linkscape/url-metrics/$url?Cols=103079215104", $curl_args );
  ```
- [wp_commenter_csv_export/ajax.php:64](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/ajax.php#L64)

  ```text
  $values[]= "($c->comment_ID, 'seomoz', '".mysql_real_escape_string($res)."')" ;
  ```
- [wp_commenter_csv_export/ajax.php:69](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/ajax.php#L69)

  ```text
  $wpdb->query("INSERT INTO $wpdb->commentmeta (comment_id, meta_key, meta_value) VALUES " .implode($values, ',') );
  ```
- [wp_commenter_csv_export/get-seomoz.php:39](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/get-seomoz.php#L39)

  ```text
  <th class="th1">Commenter&#39;s URL</th><th>Page Authority</th><th>Domain Authority</th>
  ```
- [wp_commenter_csv_export/get-seomoz.php:103](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/get-seomoz.php#L103)

  ```text
  jQuery("#upa_"+cid).html( res['upa']);
  ```
- [wp_commenter_csv_export/get-seomoz.php:104](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/get-seomoz.php#L104)

  ```text
  jQuery("#pda_"+cid).html( res['pda']);
  ```
- [wp_commenter_csv_export/commenters-csv.php:108](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/commenters-csv.php#L108)

  ```text
  $seomozdata= $wpdb->get_results("SELECT comment_id, meta_value FROM $wpdb->commentmeta WHERE meta_key='seomoz'");
  ```
- [wp_commenter_csv_export/commenters-csv.php:140](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/commenters-csv.php#L140)

  ```text
  \"Page Authority\"$sep\"Domain Authority\"
  ```
- [wp_commenter_csv_export/commenters-csv.php:146](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/commenters-csv.php#L146)

  ```text
  ( isset($upa[$res->comment_ID])?$upa[$res->comment_ID]:'' )
  ```
- [wp_commenter_csv_export/commenters-csv.php:147](https://github.com/seerinteractive/wp_commenter_csv_export/blob/bef6d64ed212fd569643cc87318d1b443aee92fe/commenters-csv.php#L147)

  ```text
  ( isset($pda[$res->comment_ID])?$pda[$res->comment_ID]:'' )
  ```

## Provenance and limits

Raw extraction: `out/wp_commenter_csv_export.json`. Independent audit: `audit/wp_commenter_csv_export.json` in the knowledge report bundle. The original whitespace-normalized `.verified.json` files are not acceptance inputs. This report does not assert a complete semantic audit of every tracked file, deployment verification, or owner approval. Excluded candidate text and audit rationale are kept in the separate review report.
