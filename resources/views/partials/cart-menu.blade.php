@if($menuItems->isNotEmpty())
<section class="cart-menu" aria-labelledby="cart-menu-title">
<div class="cart-menu-heading"><div><span class="web-eyebrow">{{ __('TAMBAHAN BUAT MAIN') }}</span><h2 id="cart-menu-title">{{ __('Mau sekalian pesan menu?') }}</h2><p>{{ __('Tambah makanan dan minuman tanpa meninggalkan keranjang.') }}</p></div><a class="web-text-link" href="{{ route('menu-fnb') }}">{{ __('Lihat kedai') }}</a></div>
@foreach($menuItems->groupBy('category') as $category=>$items)
<div class="cart-menu-category"><h3>{{ __($category) }}</h3><div class="cart-menu-grid">
@foreach($items as $item)
@php($chosen=$lines->first(fn($line)=>$line['item']->id===$item->id))
@php($quantity=$chosen['quantity']??0)
<article class="cart-menu-card"><img src="{{ \App\Support\SiteAsset::url($item->image()) }}" alt="{{ $item->name }}" width="96" height="96" loading="lazy" decoding="async"><div class="cart-menu-info"><h4>{{ $item->name }}</h4><p>{{ $item->description }}</p><strong>Rp{{ number_format($item->price,0,',','.') }}</strong><span class="cart-menu-stock">{{ __(':count tersedia', ['count'=>$item->stock]) }}</span></div>
<div class="cart-menu-control">
@if($quantity>0)
<form method="post" action="{{ $quantity===1?route('cart.destroy',$item):route('cart.update',$item) }}">@csrf @method($quantity===1?'DELETE':'PATCH')@if($quantity>1)<input type="hidden" name="quantity" value="{{ $quantity-1 }}">@endif<button type="submit" aria-label="{{ __('Kurangi :name', ['name'=>$item->name]) }}">−</button></form><span aria-label="{{ __('Jumlah') }}">{{ $quantity }}</span>
@endif
<form method="post" action="{{ route('cart.store') }}">@csrf<input type="hidden" name="catalog_item_id" value="{{ $item->id }}"><button type="submit" @disabled($quantity>=min(99,$item->stock)) aria-label="{{ __('Tambah :name', ['name'=>$item->name]) }}">{{ $quantity>0?'+':__('Tambah') }}</button></form>
</div></article>
@endforeach
</div></div>
@endforeach
</section>
@endif
