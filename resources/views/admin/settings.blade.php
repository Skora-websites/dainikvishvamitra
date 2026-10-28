@extends('layouts.admin')

@section('content')
@php
$fields = [
    'site_name' => 'साइट नाम · Site name',
    'site_title' => 'साइट शीर्षक (Meta) · Site title (Meta)',
    'tagline' => 'टैगलाइन · Tagline',
    'site_desc' => 'साइट विवरण (Meta) · Site description (Meta)',
    'edition_line' => 'संस्करण पंक्ति (हेडर तिथि) · Edition line (header date)',
    'edition_date' => 'संस्करण तिथि (राशिफल/चिप) · Edition date (horoscope/chip)',
    'weather_text' => 'मौसम पाठ · Weather text',
    'header_sensex' => 'हेडर सेंसेक्स · Header Sensex',
    'header_nifty' => 'हेडर निफ्टी · Header Nifty',
    'copyright_text' => 'कॉपीराइट पाठ (फ़ुटर) · Copyright text (footer)',
    'reg_no' => 'पंजीयन संख्या · Registration no.',
    'contact_email' => 'संपर्क ईमेल · Contact email',
    'contact_phone' => 'संपर्क फोन · Contact phone',
    'address' => 'पता · Address',
    'facebook' => 'Facebook URL',
    'twitter' => 'Twitter URL',
    'youtube' => 'YouTube URL',
    'youtube_channel' => 'YouTube चैनल URL · YouTube channel URL',
    'instagram' => 'Instagram URL',
    'epaper_url' => 'ई-पेपर लिंक · E-paper link',
    'seats_nda' => 'एनडीए सीटें (पाठ) · NDA seats (text)',
    'seats_nda_pct' => 'एनडीए % (बार) · NDA % (bar)',
    'seats_india' => 'इंडिया सीटें (पाठ) · INDIA seats (text)',
    'seats_india_pct' => 'इंडिया % (बार) · INDIA % (bar)',
    'seats_other' => 'अन्य सीटें (पाठ) · Others seats (text)',
    'seats_other_pct' => 'अन्य % (बार) · Others % (bar)',
];
@endphp
<div class="bg-white rounded-lg border p-4 max-w-3xl">
<form method="post" action="{{ route('admin.settings.save') }}" class="space-y-3 text-sm">
@csrf
@foreach($fields as $k => $label)
<div>
<label class="block text-xs font-semibold mb-1">{{ $label }} <span class="font-normal text-stone-400">({{ $k }})</span></label>
<input name="{{ $k }}" value="{{ old($k, \App\Support\Site::setting($k)) }}" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-red-800"/>
@error($k)<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
</div>
@endforeach
<button class="bg-red-800 hover:bg-red-900 text-white px-6 py-2.5 rounded font-semibold" type="submit">सहेजें · Save</button>
</form>
</div>
@endsection
