@props(['active'])



@php
$classes = ($active ?? false)
    ? 'relative ml-auto right-0 text-[#3f5efb] bg-gray-100 outline-none rounded-tl-[20px] rounded-bl-[20px] my-[10px] h-[40px] w-[90%] text-center after:absolute after:bg-transparent after:bottom-full after:right-0 after:h-[35px] after:w-[35px] after:rounded-br-[18px] after:shadow-[0_20px_0_0_rgb(243,244,246)] before:absolute before:bg-transparent before:top-[38px] before:right-0 before:h-[35px] before:w-[35px] before:rounded-tr-[18px] before:shadow-[0_-20px_0_0_rgb(243,244,246)]'
    : 'relative inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-black hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out';
@endphp



<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>