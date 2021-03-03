<?php
/**
 * Created by PhpStorm.
 * User: backend1
 * Date: 10/3/19
 * Time: 1:05 PM
 */

/** @var Property $property */
$monthly     = $property->getMonthlyPrice() ? '&amp; Monthly' : false;
$name        = $property->getName();
$thumb       = $property->getThumbnailPath();
$location    = $property->getLocation();
$perDayPrice = $property->getPerDayPrice();
$ratings     = $property->getTotalRatings();
?>

<div class="col-sm-12 col-md-4 property__card undefined">
    <div class="react-reveal reveal-up reveal-initial"
         style="animation-duration: 1000ms; animation-delay: 0ms; animation-iteration-count: 1; opacity: 1;">
        <div class="ant-spin-nested-loading">
            <div class="ant-spin-container">
                <div class="ant-card ant-card-hoverable">
                    <div class="ant-card-cover"><img 
                                                     src="<?php echo $thumb; ?>"
                                                     style="object-fit: cover; display: block;"></div>
                    <div class="ant-card-body">
                        <div class="ant-tag undefined undefined"><?php echo $monthly; ?></div>
                        <i aria-label="icon: undefined" tabindex="-1" class="anticon non-fav favourites">
                            <svg width="20px" height="20px" viewBox="0 0 20 20">
                                <g id="Tools" stroke="none" stroke-width="1" fill="none"
                                   fill-rule="evenodd">
                                    <g id="card/property-hover"
                                       transform="translate(-220.000000, -13.000000)">
                                        <g id="hover" transform="translate(102.000000, 0.000000)">
                                            <g id="baseline-favorite_border-24px"
                                               transform="translate(116.000000, 10.000000)">
                                                <polygon id="Path" points="0 0 24 0 24 24 0 24"></polygon>
                                                <path d="M16.5,3.125 C14.76,3.125 13.09,3.96875 12,5.30208333 C10.91,3.96875 9.24,3.125 7.5,3.125 C4.42,3.125 2,5.64583333 2,8.85416667 C2,12.7916667 5.4,16 10.55,20.875 L12,22.2395833 L13.45,20.8645833 C18.6,16 22,12.7916667 22,8.85416667 C22,5.64583333 19.58,3.125 16.5,3.125 Z M12.1,19.3229167 L12,19.4270833 L11.9,19.3229167 C7.14,14.8333333 4,11.8645833 4,8.85416667 C4,6.77083333 5.5,5.20833333 7.5,5.20833333 C9.04,5.20833333 10.54,6.23958333 11.07,7.66666667 L12.94,7.66666667 C13.46,6.23958333 14.96,5.20833333 16.5,5.20833333 C18.5,5.20833333 20,6.77083333 20,8.85416667 C20,11.8645833 16.86,14.8333333 12.1,19.3229167 Z"
                                                      id="Shape" fill="#FFFFFF" fill-rule="nonzero"></path>
                                            </g>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </i><span class="sub-header-2"><?php echo $name; ?></span>
                        <p class="small"><?php echo $location; ?></p>
                        <p><span>From</span> <b><?php echo $perDayPrice; ?></b> <span>per night</span></p>
                        <div class="property__card__rating">
                            <ul class="ant-rate ant-rate-disabled" tabindex="-1" role="radiogroup">
                                <li class="ant-rate-star ant-rate-star-zero">
                                    <div role="radio" aria-checked="false" aria-posinset="1"
                                         aria-setsize="5" tabindex="0">
                                        <div class="ant-rate-star-first"><i aria-label="icon: undefined"
                                                                            class="anticon">
                                                <svg width="11px" height="11px" viewBox="0 0 11 11"
                                                     fill="#939393">
                                                    <g id="design" stroke="none" stroke-width="1"
                                                       fill-rule="evenodd">
                                                        <polygon id="★" fill-rule="nonzero"
                                                                 points="5.458333 0.57 6.818333 4.27 10.338333 4.27 7.388333 6.48 8.808333 10.21 5.458333 7.7 2.108333 10.21 3.528333 6.48 0.578333 4.27 4.088333 4.27"></polygon>
                                                    </g>
                                                </svg>
                                            </i></div>
                                        <div class="ant-rate-star-second"><i aria-label="icon: undefined"
                                                                             class="anticon">
                                                <svg width="11px" height="11px" viewBox="0 0 11 11"
                                                     fill="#939393">
                                                    <g id="design" stroke="none" stroke-width="1"
                                                       fill-rule="evenodd">
                                                        <polygon id="★" fill-rule="nonzero"
                                                                 points="5.458333 0.57 6.818333 4.27 10.338333 4.27 7.388333 6.48 8.808333 10.21 5.458333 7.7 2.108333 10.21 3.528333 6.48 0.578333 4.27 4.088333 4.27"></polygon>
                                                    </g>
                                                </svg>
                                            </i></div>
                                    </div>
                                </li>
                                <li class="ant-rate-star ant-rate-star-zero">
                                    <div role="radio" aria-checked="false" aria-posinset="2"
                                         aria-setsize="5" tabindex="0">
                                        <div class="ant-rate-star-first"><i aria-label="icon: undefined"
                                                                            class="anticon">
                                                <svg width="11px" height="11px" viewBox="0 0 11 11"
                                                     fill="#939393">
                                                    <g id="design" stroke="none" stroke-width="1"
                                                       fill-rule="evenodd">
                                                        <polygon id="★" fill-rule="nonzero"
                                                                 points="5.458333 0.57 6.818333 4.27 10.338333 4.27 7.388333 6.48 8.808333 10.21 5.458333 7.7 2.108333 10.21 3.528333 6.48 0.578333 4.27 4.088333 4.27"></polygon>
                                                    </g>
                                                </svg>
                                            </i></div>
                                        <div class="ant-rate-star-second"><i aria-label="icon: undefined"
                                                                             class="anticon">
                                                <svg width="11px" height="11px" viewBox="0 0 11 11"
                                                     fill="#939393">
                                                    <g id="design" stroke="none" stroke-width="1"
                                                       fill-rule="evenodd">
                                                        <polygon id="★" fill-rule="nonzero"
                                                                 points="5.458333 0.57 6.818333 4.27 10.338333 4.27 7.388333 6.48 8.808333 10.21 5.458333 7.7 2.108333 10.21 3.528333 6.48 0.578333 4.27 4.088333 4.27"></polygon>
                                                    </g>
                                                </svg>
                                            </i></div>
                                    </div>
                                </li>
                                <li class="ant-rate-star ant-rate-star-zero">
                                    <div role="radio" aria-checked="false" aria-posinset="3"
                                         aria-setsize="5" tabindex="0">
                                        <div class="ant-rate-star-first"><i aria-label="icon: undefined"
                                                                            class="anticon">
                                                <svg width="11px" height="11px" viewBox="0 0 11 11"
                                                     fill="#939393">
                                                    <g id="design" stroke="none" stroke-width="1"
                                                       fill-rule="evenodd">
                                                        <polygon id="★" fill-rule="nonzero"
                                                                 points="5.458333 0.57 6.818333 4.27 10.338333 4.27 7.388333 6.48 8.808333 10.21 5.458333 7.7 2.108333 10.21 3.528333 6.48 0.578333 4.27 4.088333 4.27"></polygon>
                                                    </g>
                                                </svg>
                                            </i></div>
                                        <div class="ant-rate-star-second"><i aria-label="icon: undefined"
                                                                             class="anticon">
                                                <svg width="11px" height="11px" viewBox="0 0 11 11"
                                                     fill="#939393">
                                                    <g id="design" stroke="none" stroke-width="1"
                                                       fill-rule="evenodd">
                                                        <polygon id="★" fill-rule="nonzero"
                                                                 points="5.458333 0.57 6.818333 4.27 10.338333 4.27 7.388333 6.48 8.808333 10.21 5.458333 7.7 2.108333 10.21 3.528333 6.48 0.578333 4.27 4.088333 4.27"></polygon>
                                                    </g>
                                                </svg>
                                            </i></div>
                                    </div>
                                </li>
                                <li class="ant-rate-star ant-rate-star-zero">
                                    <div role="radio" aria-checked="false" aria-posinset="4"
                                         aria-setsize="5" tabindex="0">
                                        <div class="ant-rate-star-first"><i aria-label="icon: undefined"
                                                                            class="anticon">
                                                <svg width="11px" height="11px" viewBox="0 0 11 11"
                                                     fill="#939393">
                                                    <g id="design" stroke="none" stroke-width="1"
                                                       fill-rule="evenodd">
                                                        <polygon id="★" fill-rule="nonzero"
                                                                 points="5.458333 0.57 6.818333 4.27 10.338333 4.27 7.388333 6.48 8.808333 10.21 5.458333 7.7 2.108333 10.21 3.528333 6.48 0.578333 4.27 4.088333 4.27"></polygon>
                                                    </g>
                                                </svg>
                                            </i></div>
                                        <div class="ant-rate-star-second"><i aria-label="icon: undefined"
                                                                             class="anticon">
                                                <svg width="11px" height="11px" viewBox="0 0 11 11"
                                                     fill="#939393">
                                                    <g id="design" stroke="none" stroke-width="1"
                                                       fill-rule="evenodd">
                                                        <polygon id="★" fill-rule="nonzero"
                                                                 points="5.458333 0.57 6.818333 4.27 10.338333 4.27 7.388333 6.48 8.808333 10.21 5.458333 7.7 2.108333 10.21 3.528333 6.48 0.578333 4.27 4.088333 4.27"></polygon>
                                                    </g>
                                                </svg>
                                            </i></div>
                                    </div>
                                </li>
                                <li class="ant-rate-star ant-rate-star-zero">
                                    <div role="radio" aria-checked="false" aria-posinset="5"
                                         aria-setsize="5" tabindex="0">
                                        <div class="ant-rate-star-first"><i aria-label="icon: undefined"
                                                                            class="anticon">
                                                <svg width="11px" height="11px" viewBox="0 0 11 11"
                                                     fill="#939393">
                                                    <g id="design" stroke="none" stroke-width="1"
                                                       fill-rule="evenodd">
                                                        <polygon id="★" fill-rule="nonzero"
                                                                 points="5.458333 0.57 6.818333 4.27 10.338333 4.27 7.388333 6.48 8.808333 10.21 5.458333 7.7 2.108333 10.21 3.528333 6.48 0.578333 4.27 4.088333 4.27"></polygon>
                                                    </g>
                                                </svg>
                                            </i></div>
                                        <div class="ant-rate-star-second"><i aria-label="icon: undefined"
                                                                             class="anticon">
                                                <svg width="11px" height="11px" viewBox="0 0 11 11"
                                                     fill="#939393">
                                                    <g id="design" stroke="none" stroke-width="1"
                                                       fill-rule="evenodd">
                                                        <polygon id="★" fill-rule="nonzero"
                                                                 points="5.458333 0.57 6.818333 4.27 10.338333 4.27 7.388333 6.48 8.808333 10.21 5.458333 7.7 2.108333 10.21 3.528333 6.48 0.578333 4.27 4.088333 4.27"></polygon>
                                                    </g>
                                                </svg>
                                            </i></div>
                                    </div>
                                </li>
                            </ul>
                            <small><?php echo $ratings; ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
