# Change Log (shopbay-chatbot)

## Version 0.25 - Aug 5, 2018

This release is a minor patch but version is promoted to v0.25 to keep in line with other `shopbay-app` releases using v0.25 also.

### Enhancements:

 - Chg: The Sii messages at app level will be standalone and not merged with shopbay-kernel common messages.
 - Chg: Change the loading sequence of Sii messages of each module. It auto merges message in sequence:
(1) application level messages
(2) common module level messages (inclusive of kernel common messages)
(3) local module level messages

### Bug fixes:

 - Bug: Added missing folders: runtime, www/assets


## Version 0.24 - Jun 24, 2017

This is the initial release of `shopbay-chatbot`, part of Shopbay.org open source project. 

It includes code re-architecture and refactoring to separate the `chatbot` app out from old code.
All existing functions and features remain same as inherited from previous code base (v0.23.2).

For full copyright and license information, please view the [LICENSE](LICENSE.md) file that was distributed with this source code.


## Version 0.23 and before - June 2013 to March 2017

Started since June 2013 as private development, the beta version (v0.1) was released at September 2015. 

Shopbay.org open source project was created by forking from beta release v0.23.2 (f4f4b25). 