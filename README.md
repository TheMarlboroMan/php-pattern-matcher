# php-pattern-matcher

Simple class to match strings

# what is this

It matches strings against strings and allows for a level of parametrization, say

this_is_my/[value:int]/and_something_[other_value:alpha]/else

Can be matched to "this_is_my/33/and_something_word/else" in a way in which a match would exist and two values would be parametrized: 33 and "word".

# so... like regular expressions?

But slower. And with less features.

# then... why?

I needed something like this for a router and it had to fulfill a these requirements:

- The pattern syntax needed to be really easy and homogeneous.
- The people who would use it needed to be able to extend, debug and follow the code along.

# would I want to use this, how would I do it?

Check the examples directory. There are a couple of examples there that will show you how to match strings and extract parameters.
