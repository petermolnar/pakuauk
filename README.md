# Pa-Kua UK

This is a tiny website built for Pa-Kua School UK. Most of the content is in standard HTML format glued together with some vanilla PHP. It's all extremely simple and small.

## External services

- videos are embedded from Youtube
- maps are embedded from Google Maps
- newsletter form is for Mailchimp
- gallery is auto generated from Flickr and hot links to Flickr hosted images

## Data

Org and site data are in JSON-LD files in `site.json` and `org.json`. Each page has a `.html` part and a `.json` part; the JSON is JSON-LD and for metadata, while the HTML will populate the `<main>` part of the page.

## Example `flickr.json`

The required `.flickr.json` file is not in the repository, because it contains and API key. It should look like this:

```json
{
    "api_key": "KEY",
    "photoset_id": "ID",
    "user_id": "UID"
}
```
