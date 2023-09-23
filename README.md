### Description

This plugin supports the publication of conference proceedings with OJS LTS 3.3 and higher.

Development is led by [TIB](https://tib.eu) in collaboration with [PKP](https://pkp.sfu.ca) and PKP development partners.

We welcome ideas, feature requests in the [discussion](https://github.com/withanage/conference/discussions/) section.


### Supported Metadata
| Field       | Values                  | Optional | Issue                                            | Version |
|-------------|-------------------------|----------|--------------------------------------------------|----|
| Conference type | online                  | no       | https://github.com/withanage/conference/issues/5 | 3.3   |
|  Name | Current issue name      | no       | https://github.com/withanage/conference/issues/2 | 3.3   |
| Place       | Location, City, Country | yes      | https://github.com/withanage/conference/issues/3 | 3.3 |
| Date        | Begin Date and End-Date | yes      | https://github.com/withanage/conference/issues/4 | 3.3|



### Installation

```bash
# Software download
cd $OJS/plugins/generic
git clone  https://github.com/withanage/conference

# configuration
> Go to Plugins e.g. $JOURNAL/management/settings/website#plugins/installedPlugins
> Enable Plugin: Support Conferences

```


### Concept, Dev-Lead

- [https://github.com/withanage](mailto:dulip.withanage@gmail.com)








