<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <info@getid3.org>               //
//  available at http://getid3.sourceforge.net                 //
//            or http://www.getid3.org                         //
//          also https://github.com/JamesHeinrich/getID3       //
/////////////////////////////////////////////////////////////////
// See readme.txt for more details                             //
/////////////////////////////////////////////////////////////////
///                                                            //
// module.tag.id3v2.php                                        //
// module for analyzing ID3v2 tags                             //
// dependencies: module.tag.id3v1.php                          //
//                                                            ///
/////////////////////////////////////////////////////////////////

getid3_lib::IncludeDependency(GETID3_INCLUDEPATH . 'module.tag.id3v1.php', __FILE__, true);

class getid3_id3v2 extends getid3_handler
{
    public $StartingOffset = 0;

    public function Analyze() {
        $info = &$this->getid3->info;

        //    Overall tag structure:
        //        +-----------------------------+
        //        |      Header (10 bytes)      |
        //        +-----------------------------+
        //        |       Extended Header       |
        //        | (variable length, OPTIONAL) |
        //        +-----------------------------+
        //        |   Frames (variable length)  |
        //        +-----------------------------+
        //        |           Padding           |
        //        | (variable length, OPTIONAL) |
        //        +-----------------------------+
        //        | Footer (10 bytes, OPTIONAL) |
        //        +-----------------------------+

        //    Header
        //        ID3v2/file identifier      "ID3"
        //        ID3v2 version              $04 00
        //        ID3v2 flags                (%ab000000 in v2.2, %abc00000 in v2.3, %abcd0000 in v2.4.x)
        //        ID3v2 size             4 * %0xxxxxxx

        // shortcuts
        $info['id3v2']['header'] = true;
        $thisfile_id3v2                  = &$info['id3v2'];
        $thisfile_id3v2['flags']         =  array();
        $thisfile_id3v2_flags            = &$thisfile_id3v2['flags'];

        $this->fseek($this->StartingOffset);
        $header = $this->fread(10);
        if (substr($header, 0, 3) == 'ID3'  &&  strlen($header) == 10) {

            $thisfile_id3v2['majorversion'] = ord($header{3});
            $thisfile_id3v2['minorversion'] = ord($header{4});

            // shortcut
            $id3v2_majorversion = &$thisfile_id3v2['majorversion'];

        } else {

            unset($info['id3v2']);

            return false;

        }

        if ($id3v2_majorversion > 4) { // this script probably won't correctly parse ID3v2.5.x and above (if it ever exists)

            $info['error'][] = 'this script only parses up to ID3v2.4.x - this tag is ID3v2.' . $id3v2_majorversion . '.' . $thisfile_id3v2['minorversion'];

            return false;

        }

        $id3_flags = ord($header{5});
        switch ($id3v2_majorversion) {
            case 2:
                // %ab000000 in v2.2
                $thisfile_id3v2_flags['unsynch']     = (bool) ($id3_flags & 0x80); // a - Unsynchronisation
                $thisfile_id3v2_flags['compression'] = (bool) ($id3_flags & 0x40); // b - Compression
                break;

            case 3:
                // %abc00000 in v2.3
                $thisfile_id3v2_flags['unsynch']     = (bool) ($id3_flags & 0x80); // a - Unsynchronisation
                $thisfile_id3v2_flags['exthead']     = (bool) ($id3_flags & 0x40); // b - Extended header
                $thisfile_id3v2_flags['experim']     = (bool) ($id3_flags & 0x20); // c - Experimental indicator
                break;

            case 4:
                // %abcd0000 in v2.4
                $thisfile_id3v2_flags['unsynch']     = (bool) ($id3_flags & 0x80); // a - Unsynchronisation
                $thisfile_id3v2_flags['exthead']     = (bool) ($id3_flags & 0x40); // b - Extended header
                $thisfile_id3v2_flags['experim']     = (bool) ($id3_flags & 0x20); // c - Experimental indicator
                $thisfile_id3v2_flags['isfooter']    = (bool) ($id3_flags & 0x10); // d - Footer present
                break;
        }

        $thisfile_id3v2['headerlength'] = getid3_lib::BigEndian2Int(substr($header, 6, 4), 1) + 10; // length of ID3v2 tag in 10-byte header doesn't include 10-byte header length

        $thisfile_id3v2['tag_offset_start'] = $this->StartingOffset;
        $thisfile_id3v2['tag_offset_end']   = $thisfile_id3v2['tag_offset_start'] + $thisfile_id3v2['headerlength'];

        // create 'encoding' key - used by getid3::HandleAllTags()
        // in ID3v2 every field can have it's own encoding type
        // so force everything to UTF-8 so it can be handled consistantly
        $thisfile_id3v2['encoding'] = 'UTF-8';

    //    Frames

    //        All ID3v2 frames consists of one frame header followed by one or more
    //        fields containing the actual information. The header is always 10
    //        bytes and laid out as follows:
    //
    //        Frame ID      $xx xx xx xx  (four characters)
    //        Size      4 * %0xxxxxxx
    //        Flags         $xx xx

        $sizeofframes = $thisfile_id3v2['headerlength'] - 10; // not including 10-byte initial header
        if (!empty($thisfile_id3v2['exthead']['length'])) {
            $sizeofframes -= ($thisfile_id3v2['exthead']['length'] + 4);
        }
        if (!empty($thisfile_id3v2_flags['isfooter'])) {
            $sizeofframes -= 10; // footer takes last 10 bytes of ID3v2 header, after frame data, before audio
        }
        if ($sizeofframes > 0) {

            $framedata = $this->fread($sizeofframes); // read all frames from file into $framedata variable

            //    if entire frame data is unsynched, de-unsynch it now (ID3v2.3.x)
            if (!empty($thisfile_id3v2_flags['unsynch']) && ($id3v2_majorversion <= 3)) {
                $framedata = $this->DeUnsynchronise($framedata);
            }
            //        [in ID3v2.4.0] Unsynchronisation [S:6.1] is done on frame level, instead
            //        of on tag level, making it easier to skip frames, increasing the streamability
            //        of the tag. The unsynchronisation flag in the header [S:3.1] indicates that
            //        there exists an unsynchronised frame, while the new unsynchronisation flag in
            //        the frame header [S:4.1.2] indicates unsynchronisation.

            //$framedataoffset = 10 + ($thisfile_id3v2['exthead']['length'] ? $thisfile_id3v2['exthead']['length'] + 4 : 0); // how many bytes into the stream - start from after the 10-byte header (and extended header length+4, if present)
            $framedataoffset = 10; // how many bytes into the stream - start from after the 10-byte header

            //    Extended Header
            if (!empty($thisfile_id3v2_flags['exthead'])) {
                $extended_header_offset = 0;

                if ($id3v2_majorversion == 3) {

                    // v2.3 definition:
                    //Extended header size  $xx xx xx xx   // 32-bit integer
                    //Extended Flags        $xx xx
                    //     %x0000000 %00000000 // v2.3
                    //     x - CRC data present
                    //Size of padding       $xx xx xx xx

                    $thisfile_id3v2['exthead']['length'] = getid3_lib::BigEndian2Int(substr($framedata, $extended_header_offset, 4), 0);
                    $extended_header_offset += 4;

                    $thisfile_id3v2['exthead']['flag_bytes'] = 2;
                    $thisfile_id3v2['exthead']['flag_raw'] = getid3_lib::BigEndian2Int(substr($framedata, $extended_header_offset, $thisfile_id3v2['exthead']['flag_bytes']));
                    $extended_header_offset += $thisfile_id3v2['exthead']['flag_bytes'];

                    $thisfile_id3v2['exthead']['flags']['crc'] = (bool) ($thisfile_id3v2['exthead']['flag_raw'] & 0x8000);

                    $thisfile_id3v2['exthead']['padding_size'] = getid3_lib::BigEndian2Int(substr($framedata, $extended_header_offset, 4));
                    $extended_header_offset += 4;

                    if ($thisfile_id3v2['exthead']['flags']['crc']) {
                        $thisfile_id3v2['exthead']['flag_data']['crc'] = getid3_lib::BigEndian2Int(substr($framedata, $extended_header_offset, 4));
                        $extended_header_offset += 4;
                    }
                    $extended_header_offset += $thisfile_id3v2['exthead']['padding_size'];

                } elseif ($id3v2_majorversion == 4) {

                    // v2.4 definition:
                    //Extended header size   4 * %0xxxxxxx // 28-bit synchsafe integer
                    //Number of flag bytes       $01
                    //Extended Flags             $xx
                    //     %0bcd0000 // v2.4
                    //     b - Tag is an update
                    //         Flag data length       $00
                    //     c - CRC data present
                    //         Flag data length       $05
                    //         Total frame CRC    5 * %0xxxxxxx
                    //     d - Tag restrictions
                    //         Flag data length       $01

                    $thisfile_id3v2['exthead']['length'] = getid3_lib::BigEndian2Int(substr($framedata, $extended_header_offset, 4), true);
                    $extended_header_offset += 4;

                    $thisfile_id3v2['exthead']['flag_bytes'] = getid3_lib::BigEndian2Int(substr($framedata, $extended_header_offset, 1)); // should always be 1
                    $extended_header_offset += 1;

                    $thisfile_id3v2['exthead']['flag_raw'] = getid3_lib::BigEndian2Int(substr($framedata, $extended_header_offset, $thisfile_id3v2['exthead']['flag_bytes']));
                    $extended_header_offset += $thisfile_id3v2['exthead']['flag_bytes'];

                    $thisfile_id3v2['exthead']['flags']['update']       = (bool) ($thisfile_id3v2['exthead']['flag_raw'] & 0x40);
                    $thisfile_id3v2['exthead']['flags']['crc']          = (bool) ($thisfile_id3v2['exthead']['flag_raw'] & 0x20);
                    $thisfile_id3v2['exthead']['flags']['restrictions'] = (bool) ($thisfile_id3v2['exthead']['flag_raw'] & 0x10);

                    if ($thisfile_id3v2['exthead']['flags']['update']) {
                        $ext_header_chunk_length = getid3_lib::BigEndian2Int(substr($framedata, $extended_header_offset, 1)); // should be 0
                        $extended_header_offset += 1;
                    }

                    if ($thisfile_id3v2['exthead']['flags']['crc']) {
                        $ext_header_chunk_length = getid3_lib::BigEndian2Int(substr($framedata, $extended_header_offset, 1)); // should be 5
                        $extended_header_offset += 1;
                        $thisfile_id3v2['exthead']['flag_data']['crc'] = getid3_lib::BigEndian2Int(substr($framedata, $extended_header_offset, $ext_header_chunk_length), true, false);
                        $extended_header_offset += $ext_header_chunk_length;
                    }

                    if ($thisfile_id3v2['exthead']['flags']['restrictions']) {
                        $ext_header_chunk_length = getid3_lib::BigEndian2Int(substr($framedata, $extended_header_offset, 1)); // should be 1
                        $extended_header_offset += 1;

                        // %ppqrrstt
                        $restrictions_raw = getid3_lib::BigEndian2Int(substr($framedata, $extended_header_offset, 1));
                        $extended_header_offset += 1;
                        $thisfile_id3v2['exthead']['flags']['restrictions']['tagsize']  = ($restrictions_raw & 0xC0) >> 6; // p - Tag size restrictions
                        $thisfile_id3v2['exthead']['flags']['restrictions']['textenc']  = ($restrictions_raw & 0x20) >> 5; // q - Text encoding restrictions
                        $thisfile_id3v2['exthead']['flags']['restrictions']['textsize'] = ($restrictions_raw & 0x18) >> 3; // r - Text fields size restrictions
                        $thisfile_id3v2['exthead']['flags']['restrictions']['imgenc']   = ($restrictions_raw & 0x04) >> 2; // s - Image encoding restrictions
                        $thisfile_id3v2['exthead']['flags']['restrictions']['imgsize']  = ($restrictions_raw & 0x03) >> 0; // t - Image size restrictions

                        $thisfile_id3v2['exthead']['flags']['restrictions_text']['tagsize']  = $this->LookupExtendedHeaderRestrictionsTagSizeLimits($thisfile_id3v2['exthead']['flags']['restrictions']['tagsize']);
                        $thisfile_id3v2['exthead']['flags']['restrictions_text']['textenc']  = $this->LookupExtendedHeaderRestrictionsTextEncodings($thisfile_id3v2['exthead']['flags']['restrictions']['textenc']);
                        $thisfile_id3v2['exthead']['flags']['restrictions_text']['textsize'] = $this->LookupExtendedHeaderRestrictionsTextFieldSize($thisfile_id3v2['exthead']['flags']['restrictions']['textsize']);
                        $thisfile_id3v2['exthead']['flags']['restrictions_text']['imgenc']   = $this->LookupExtendedHeaderRestrictionsImageEncoding($thisfile_id3v2['exthead']['flags']['restrictions']['imgenc']);
                        $thisfile_id3v2['exthead']['flags']['restrictions_text']['imgsize']  = $this->LookupExtendedHeaderRestrictionsImageSizeSize($thisfile_id3v2['exthead']['flags']['restrictions']['imgsize']);
                    }

                    if ($thisfile_id3v2['exthead']['length'] != $extended_header_offset) {
                        $info['warning'][] = 'ID3v2.4 extended header length mismatch (expecting ' . intval($thisfile_id3v2['exthead']['length']) . ', found ' . intval($extended_header_offset) . ')';
                    }
                }

                $framedataoffset += $extended_header_offset;
                $framedata = substr($framedata, $extended_header_offset);
            } // end extended header

            while (isset($framedata) && (strlen($framedata) > 0)) { // cycle through until no more frame data is left to parse
                if (strlen($framedata) <= $this->ID3v2HeaderLength($id3v2_majorversion)) {
                    // insufficient room left in ID3v2 header for actual data - must be padding
                    $thisfile_id3v2['padding']['start']  = $framedataoffset;
                    $thisfile_id3v2['padding']['length'] = strlen($framedata);
                    $thisfile_id3v2['padding']['valid']  = true;
                    for ($i = 0; $i < $thisfile_id3v2['padding']['length']; $i++) {
                        if ($framedata{$i} != "\x00") {
                            $thisfile_id3v2['padding']['valid'] = false;
                            $thisfile_id3v2['padding']['errorpos'] = $thisfile_id3v2['padding']['start'] + $i;
                            $info['warning'][] = 'Invalid ID3v2 padding found at offset ' . $thisfile_id3v2['padding']['errorpos'] . ' (the remaining ' . ($thisfile_id3v2['padding']['length'] - $i) . ' bytes are considered invalid)';
                            break;
                        }
                    }
                    break; // skip rest of ID3v2 header
                }
                if ($id3v2_majorversion == 2) {
                    // Frame ID  $xx xx xx (three characters)
                    // Size      $xx xx xx (24-bit integer)
                    // Flags     $xx xx

                    $frame_header = substr($framedata, 0, 6); // take next 6 bytes for header
                    $framedata    = substr($framedata, 6);    // and leave the rest in $framedata
                    $frame_name   = substr($frame_header, 0, 3);
                    $frame_size   = getid3_lib::BigEndian2Int(substr($frame_header, 3, 3), 0);
                    $frame_flags  = 0; // not used for anything in ID3v2.2, just set to avoid E_NOTICEs

                } elseif ($id3v2_majorversion > 2) {

                    // Frame ID  $xx xx xx xx (four characters)
                    // Size      $xx xx xx xx (32-bit integer in v2.3, 28-bit synchsafe in v2.4+)
                    // Flags     $xx xx

                    $frame_header = substr($framedata, 0, 10); // take next 10 bytes for header
                    $framedata    = substr($framedata, 10);    // and leave the rest in $framedata

                    $frame_name = substr($frame_header, 0, 4);
                    if ($id3v2_majorversion == 3) {
                        $frame_size = getid3_lib::BigEndian2Int(substr($frame_header, 4, 4), 0); // 32-bit integer
                    } else { // ID3v2.4+
                        $frame_size = getid3_lib::BigEndian2Int(substr($frame_header, 4, 4), 1); // 32-bit synchsafe integer (28-bit value)
                    }

                    if ($frame_size < (strlen($framedata) + 4)) {
                        $nextFrameID = substr($framedata, $frame_size, 4);
                        if ($this->IsValidID3v2FrameName($nextFrameID, $id3v2_majorversion)) {
                            // next frame is OK
                        } elseif (($frame_name == "\x00" . 'MP3') || ($frame_name == "\x00\x00" . 'MP') || ($frame_name == ' MP3') || ($frame_name == 'MP3e')) {
                            // MP3ext known broken frames - "ok" for the purposes of this test
                        } elseif (($id3v2_majorversion == 4) && ($this->IsValidID3v2FrameName(substr($framedata, getid3_lib::BigEndian2Int(substr($frame_header, 4, 4), 0), 4), 3))) {
                            $info['warning'][] = 'ID3v2 tag written as ID3v2.4, but with non-synchsafe integers (ID3v2.3 style). Older versions of (Helium2; iTunes) are known culprits of this. Tag has been parsed as ID3v2.3';
                            $id3v2_majorversion = 3;
                            $frame_size = getid3_lib::BigEndian2Int(substr($frame_header, 4, 4), 0); // 32-bit integer
                        }
                    }

                    $frame_flags = getid3_lib::BigEndian2Int(substr($frame_header, 8, 2));
                }

                if ((($id3v2_majorversion == 2) && ($frame_name == "\x00\x00\x00")) || ($frame_name == "\x00\x00\x00\x00")) {
                    // padding encountered

                    $thisfile_id3v2['padding']['start']  = $framedataoffset;
                    $thisfile_id3v2['padding']['length'] = strlen($frame_header) + strlen($framedata);
                    $thisfile_id3v2['padding']['valid']  = true;

                    $len = strlen($framedata);
                    for ($i = 0; $i < $len; $i++) {
                        if ($framedata{$i} != "\x00") {
                            $thisfile_id3v2['padding']['valid'] = false;
                            $thisfile_id3v2['padding']['errorpos'] = $thisfile_id3v2['padding']['start'] + $i;
                            $info['warning'][] = 'Invalid ID3v2 padding found at offset ' . $thisfile_id3v2['padding']['errorpos'] . ' (the remaining ' . ($thisfile_id3v2['padding']['length'] - $i) . ' bytes are considered invalid)';
                            break;
                        }
                    }
                    break; // skip rest of ID3v2 header
                }

                if ($frame_name == 'COM ') {
                    $info['warning'][] = 'error parsing "' . $frame_name . '" (' . $framedataoffset . ' bytes into the ID3v2.' . $id3v2_majorversion . ' tag). (ERROR: IsValidID3v2FrameName("' . str_replace("\x00", ' ', $frame_name) . '", ' . $id3v2_majorversion . '))). [Note: this particular error has been known to happen with tags edited by iTunes (versions "X v2.0.3", "v3.0.1" are known-guilty, probably others too)]';
                    $frame_name = 'COMM';
                }
                if (($frame_size <= strlen($framedata)) && ($this->IsValidID3v2FrameName($frame_name, $id3v2_majorversion))) {

                    unset($parsedFrame);
                    $parsedFrame['frame_name']      = $frame_name;
                    $parsedFrame['frame_flags_raw'] = $frame_flags;
                    $parsedFrame['data']            = substr($framedata, 0, $frame_size);
                    $parsedFrame['datalength']      = getid3_lib::CastAsInt($frame_size);
                    $parsedFrame['dataoffset']      = $framedataoffset;

                    $this->ParseID3v2Frame($parsedFrame);
                    $thisfile_id3v2[$frame_name][] = $parsedFrame;

                    $framedata = substr($framedata, $frame_size);

                } else { // invalid frame length or FrameID

                    if ($frame_size <= strlen($framedata)) {

                        if ($this->IsValidID3v2FrameName(substr($framedata, $frame_size, 4), $id3v2_majorversion)) {

                            // next frame is valid, just skip the current frame
                            $framedata = substr($framedata, $frame_size);
                            $info['warning'][] = 'Next ID3v2 frame is valid, skipping current frame.';

                        } else {

                            // next frame is invalid too, abort processing
                            //unset($framedata);
                            $framedata = null;
                            $info['error'][] = 'Next ID3v2 frame is also invalid, aborting processing.';

                        }

                    } elseif ($frame_size == strlen($framedata)) {

                        // this is the last frame, just skip
                        $info['warning'][] = 'This was the last ID3v2 frame.';

                    } else {

                        // next frame is invalid too, abort processing
                        //unset($framedata);
                        $framedata = null;
                        $info['warning'][] = 'Invalid ID3v2 frame size, aborting.';

                    }
                    if (!$this->IsValidID3v2FrameName($frame_name, $id3v2_majorversion)) {

                        switch ($frame_name) {
                            case "\x00\x00" . 'MP':
                            case "\x00" . 'MP3':
                            case ' MP3':
                            case 'MP3e':
                            case "\x00" . 'MP':
                            case ' MP':
                            case 'MP3':
                                $info['warning'][] = 'error parsing "' . $frame_name . '" (' . $framedataoffset . ' bytes into the ID3v2.' . $id3v2_majorversion . ' tag). (ERROR: !IsValidID3v2FrameName("' . str_replace("\x00", ' ', $frame_name) . '", ' . $id3v2_majorversion . '))). [Note: this particular error has been known to happen with tags edited by "MP3ext (www.mutschler.de/mp3ext/)"]';
                                break;

                            default:
                                $info['warning'][] = 'error parsing "' . $frame_name . '" (' . $framedataoffset . ' bytes into the ID3v2.' . $id3v2_majorversion . ' tag). (ERROR: !IsValidID3v2FrameName("' . str_replace("\x00", ' ', $frame_name) . '", ' . $id3v2_majorversion . '))).';
                                break;
                        }

                    } elseif (!isset($framedata) || ($frame_size > strlen($framedata))) {

                        $info['error'][] = 'error parsing "' . $frame_name . '" (' . $framedataoffset . ' bytes into the ID3v2.' . $id3v2_majorversion . ' tag). (ERROR: $frame_size (' . $frame_size . ') > strlen($framedata) (' . (isset($framedata) ? strlen($framedata) : 'null') . ')).';

                    } else {

                        $info['error'][] = 'error parsing "' . $frame_name . '" (' . $framedataoffset . ' bytes into the ID3v2.' . $id3v2_majorversion . ' tag).';

                    }

                }
                $framedataoffset += ($frame_size + $this->ID3v2HeaderLength($id3v2_majorversion));

            }

        }

    //    Footer

    //    The footer is a copy of the header, but with a different identifier.
    //        ID3v2 identifier           "3DI"
    //        ID3v2 version              $04 00
    //        ID3v2 flags                %abcd0000
    //        ID3v2 size             4 * %0xxxxxxx

        if (isset($thisfile_id3v2_flags['isfooter']) && $thisfile_id3v2_flags['isfooter']) {
            $footer = $this->fread(10);
            if (substr($footer, 0, 3) == '3DI') {
                $thisfile_id3v2['footer'] = true;
                $thisfile_id3v2['majorversion_footer'] = ord($footer{3});
                $thisfile_id3v2['minorversion_footer'] = ord($footer{4});
            }
            if ($thisfile_id3v2['majorversion_footer'] <= 4) {
                $id3_flags = ord(substr($footer{5}));
                $thisfile_id3v2_flags['unsynch_footer']  = (bool) ($id3_flags & 0x80);
                $thisfile_id3v2_flags['extfoot_footer']  = (bool) ($id3_flags & 0x40);
                $thisfile_id3v2_flags['experim_footer']  = (bool) ($id3_flags & 0x20);
                $thisfile_id3v2_flags['isfooter_footer'] = (bool) ($id3_flags & 0x10);

                $thisfile_id3v2['footerlength'] = getid3_lib::BigEndian2Int(substr($footer, 6, 4), 1);
            }
        } // end footer

        if (isset($thisfile_id3v2['comments']['genre'])) {
            foreach ($thisfile_id3v2['comments']['genre'] as $key => $value) {
                unset($thisfile_id3v2['comments']['genre'][$key]);
                $thisfile_id3v2['comments'] = getid3_lib::array_merge_noclobber($thisfile_id3v2['comments'], array('genre'=>$this->ParseID3v2GenreString($value)));
            }
        }

        if (isset($thisfile_id3v2['comments']['track'])) {
            foreach ($thisfile_id3v2['comments']['track'] as $key => $value) {
                if (strstr($value, '/')) {
                    list($thisfile_id3v2['comments']['tracknum'][$key], $thisfile_id3v2['comments']['totaltracks'][$key]) = explode('/', $thisfile_id3v2['comments']['track'][$key]);
                }
            }
        }

        if (!isset($thisfile_id3v2['comments']['year']) && !empty($thisfile_id3v2['comments']['recording_time'][0]) && preg_match('#^([0-9]{4})#', trim($thisfile_id3v2['comments']['recording_time'][0]), $matches)) {
            $thisfile_id3v2['comments']['year'] = array($matches[1]);
        }

        if (!empty($thisfile_id3v2['TXXX'])) {
            // MediaMonkey does this, maybe others: write a blank RGAD frame, but put replay-gain adjustment values in TXXX frames
            foreach ($thisfile_id3v2['TXXX'] as $txxx_array) {
                switch ($txxx_array['description']) {
                    case 'replaygain_track_gain':
                        if (empty($info['replay_gain']['track']['adjustment']) && !empty($txxx_array['data'])) {
                            $info['replay_gain']['track']['adjustment'] = floatval(trim(str_replace('dB', '', $txxx_array['data'])));
                        }
                        break;
                    case 'replaygain_track_peak':
                        if (empty($info['replay_gain']['track']['peak']) && !empty($txxx_array['data'])) {
                            $info['replay_gain']['track']['peak'] = floatval($txxx_array['data']);
                        }
                        break;
                    case 'replaygain_album_gain':
                        if (empty($info['replay_gain']['album']['adjustment']) && !empty($txxx_array['data'])) {
                            $info['replay_gain']['album']['adjustment'] = floatval(trim(str_replace('dB', '', $txxx_array['data'])));
                        }
                        break;
                }
            }
        }

        // Set avdataoffset
        $info['avdataoffset'] = $thisfile_id3v2['headerlength'];
        if (isset($thisfile_id3v2['footer'])) {
            $info['avdataoffset'] += 10;
        }

        return true;
    }

    public function ParseID3v2GenreString($genrestring) {
        // Parse genres into arrays of genreName and genreID
        // ID3v2.2.x, ID3v2.3.x: '(21)' or '(4)Eurodisco' or '(51)(39)' or '(55)((I think...)'
        // ID3v2.4.x: '21' $00 'Eurodisco' $00
        $clean_genres = array();
        if (strpos($genrestring, "\x00") === false) {
            $genrestring = preg_replace('#\(([0-9]{1,3})\)#', '$1' . "\x00", $genrestring);
        }
        $genre_elements = explode("\x00", $genrestring);
        foreach ($genre_elements as $element) {
            $element = trim($element);
            if ($element) {
                if (preg_match('#^[0-9]{1,3}#', $element)) {
                    $clean_genres[] = getid3_id3v1::LookupGenreName($element);
                } else {
                    $clean_genres[] = str_replace('((', '(', $element);
                }
            }
        }

        return $clean_genres;
    }

    public function ParseID3v2Frame(&$parsedFrame) {

        // shortcuts
        $info = &$this->getid3->info;
        $id3v2_majorversion = $info['id3v2']['majorversion'];

        $parsedFrame['framenamelong']  = $this->FrameNameLongLookup($parsedFrame['frame_name']);
        if (empty($parsedFrame['framenamelong'])) {
            unset($parsedFrame['framenamelong']);
        }
        $parsedFrame['framenameshort'] = $this->FrameNameShortLookup($parsedFrame['frame_name']);
        if (empty($parsedFrame['framenameshort'])) {
            unset($parsedFrame['framenameshort']);
        }

        if ($id3v2_majorversion >= 3) { // frame flags are not part of the ID3v2.2 standard
            if ($id3v2_majorversion == 3) {
                //    Frame Header Flags
                //    %abc00000 %ijk00000
                $parsedFrame['flags']['TagAlterPreservation']  = (bool) ($parsedFrame['frame_flags_raw'] & 0x8000); // a - Tag alter preservation
                $parsedFrame['flags']['FileAlterPreservation'] = (bool) ($parsedFrame['frame_flags_raw'] & 0x4000); // b - File alter preservation
                $parsedFrame['flags']['ReadOnly']              = (bool) ($parsedFrame['frame_flags_raw'] & 0x2000); // c - Read only
                $parsedFrame['flags']['compression']           = (bool) ($parsedFrame['frame_flags_raw'] & 0x0080); // i - Compression
                $parsedFrame['flags']['Encryption']            = (bool) ($parsedFrame['frame_flags_raw'] & 0x0040); // j - Encryption
                $parsedFrame['flags']['GroupingIdentity']      = (bool) ($parsedFrame['frame_flags_raw'] & 0x0020); // k - Grouping identity

            } elseif ($id3v2_majorversion == 4) {
                //    Frame Header Flags
                //    %0abc0000 %0h00kmnp
                $parsedFrame['flags']['TagAlterPreservation']  = (bool) ($parsedFrame['frame_flags_raw'] & 0x4000); // a - Tag alter preservation
                $parsedFrame['flags']['FileAlterPreservation'] = (bool) ($parsedFrame['frame_flags_raw'] & 0x2000); // b - File alter preservation
                $parsedFrame['flags']['ReadOnly']              = (bool) ($parsedFrame['frame_flags_raw'] & 0x1000); // c - Read only
                $parsedFrame['flags']['GroupingIdentity']      = (bool) ($parsedFrame['frame_flags_raw'] & 0x0040); // h - Grouping identity
                $parsedFrame['flags']['compression']           = (bool) ($parsedFrame['frame_flags_raw'] & 0x0008); // k - Compression
                $parsedFrame['flags']['Encryption']            = (bool) ($parsedFrame['frame_flags_raw'] & 0x0004); // m - Encryption
                $parsedFrame['flags']['Unsynchronisation']     = (bool) ($parsedFrame['frame_flags_raw'] & 0x0002); // n - Unsynchronisation
                $parsedFrame['flags']['DataLengthIndicator']   = (bool) ($parsedFrame['frame_flags_raw'] & 0x0001); // p - Data length indicator

                // Frame-level de-unsynchronisation - ID3v2.4
                if ($parsedFrame['flags']['Unsynchronisation']) {
                    $parsedFrame['data'] = $this->DeUnsynchronise($parsedFrame['data']);
                }

                if ($parsedFrame['flags']['DataLengthIndicator']) {
                    $parsedFrame['data_length_indicator'] = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], 0, 4), 1);
                    $parsedFrame['data']                  =                           substr($parsedFrame['data'], 4);
                }
            }

            //    Frame-level de-compression
            if ($parsedFrame['flags']['compression']) {
                $parsedFrame['decompressed_size'] = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], 0, 4));
                if (!function_exists('gzuncompress')) {
                    $info['warning'][] = 'gzuncompress() support required to decompress ID3v2 frame "' . $parsedFrame['frame_name'] . '"';
                } else {
                    if ($decompresseddata = @gzuncompress(substr($parsedFrame['data'], 4))) {
                    //if ($decompresseddata = @gzuncompress($parsedFrame['data'])) {
                        $parsedFrame['data'] = $decompresseddata;
                        unset($decompresseddata);
                    } else {
                        $info['warning'][] = 'gzuncompress() failed on compressed contents of ID3v2 frame "' . $parsedFrame['frame_name'] . '"';
                    }
                }
            }
        }

        if (!empty($parsedFrame['flags']['DataLengthIndicator'])) {
            if ($parsedFrame['data_length_indicator'] != strlen($parsedFrame['data'])) {
                $info['warning'][] = 'ID3v2 frame "' . $parsedFrame['frame_name'] . '" should be ' . $parsedFrame['data_length_indicator'] . ' bytes long according to DataLengthIndicator, but found ' . strlen($parsedFrame['data']) . ' bytes of data';
            }
        }

        if (isset($parsedFrame['datalength']) && ($parsedFrame['datalength'] == 0)) {

            $warning = 'Frame "' . $parsedFrame['frame_name'] . '" at offset ' . $parsedFrame['dataoffset'] . ' has no data portion';
            switch ($parsedFrame['frame_name']) {
                case 'WCOM':
                    $warning .= ' (this is known to happen with files tagged by RioPort)';
                    break;

                default:
                    break;
            }
            $info['warning'][] = $warning;

        } elseif ((($id3v2_majorversion >= 3) && ($parsedFrame['frame_name'] == 'UFID')) || // 4.1   UFID Unique file identifier
            (($id3v2_majorversion == 2) && ($parsedFrame['frame_name'] == 'UFI'))) {  // 4.1   UFI  Unique file identifier
            //   There may be more than one 'UFID' frame in a tag,
            //   but only one with the same 'Owner identifier'.
            // <Header for 'Unique file identifier', ID: 'UFID'>
            // Owner identifier        <text string> $00
            // Identifier              <up to 64 bytes binary data>
            $exploded = explode("\x00", $parsedFrame['data'], 2);
            $parsedFrame['ownerid'] = (isset($exploded[0]) ? $exploded[0] : '');
            $parsedFrame['data']    = (isset($exploded[1]) ? $exploded[1] : '');

        } elseif ((($id3v2_majorversion >= 3) && ($parsedFrame['frame_name'] == 'TXXX')) || // 4.2.2 TXXX User defined text information frame
                (($id3v2_majorversion == 2) && ($parsedFrame['frame_name'] == 'TXX'))) {    // 4.2.2 TXX  User defined text information frame
            //   There may be more than one 'TXXX' frame in each tag,
            //   but only one with the same description.
            // <Header for 'User defined text information frame', ID: 'TXXX'>
            // Text encoding     $xx
            // Description       <text string according to encoding> $00 (00)
            // Value             <text string according to encoding>

            $frame_offset = 0;
            $frame_textencoding = ord(substr($parsedFrame['data'], $frame_offset++, 1));

            if ((($id3v2_majorversion <= 3) && ($frame_textencoding > 1)) || (($id3v2_majorversion == 4) && ($frame_textencoding > 3))) {
                $info['warning'][] = 'Invalid text encoding byte (' . $frame_textencoding . ') in frame "' . $parsedFrame['frame_name'] . '" - defaulting to ISO-8859-1 encoding';
            }
            $frame_terminatorpos = strpos($parsedFrame['data'], $this->TextEncodingTerminatorLookup($frame_textencoding), $frame_offset);
            if (ord(substr($parsedFrame['data'], $frame_terminatorpos + strlen($this->TextEncodingTerminatorLookup($frame_textencoding)), 1)) === 0) {
                $frame_terminatorpos++; // strpos() fooled because 2nd byte of Unicode chars are often 0x00
            }
            $frame_description = substr($parsedFrame['data'], $frame_offset, $frame_terminatorpos - $frame_offset);
            if (ord($frame_description) === 0) {
                $frame_description = '';
            }
            $parsedFrame['encodingid']  = $frame_textencoding;
            $parsedFrame['encoding']    = $this->TextEncodingNameLookup($frame_textencoding);

            $parsedFrame['description'] = $frame_description;
            $parsedFrame['data'] = substr($parsedFrame['data'], $frame_terminatorpos + strlen($this->TextEncodingTerminatorLookup($frame_textencoding)));
            if (!empty($parsedFrame['framenameshort']) && !empty($parsedFrame['data'])) {
                $commentkey = ($parsedFrame['description'] ? $parsedFrame['description'] : (isset($info['id3v2']['comments'][$parsedFrame['framenameshort']]) ? count($info['id3v2']['comments'][$parsedFrame['framenameshort']]) : 0));
                if (!isset($info['id3v2']['comments'][$parsedFrame['framenameshort']]) || !array_key_exists($commentkey, $info['id3v2']['comments'][$parsedFrame['framenameshort']])) {
                    $info['id3v2']['comments'][$parsedFrame['framenameshort']][$commentkey] = trim(getid3_lib::iconv_fallback($parsedFrame['encoding'], $info['id3v2']['encoding'], $parsedFrame['data']));
                } else {
                    $info['id3v2']['comments'][$parsedFrame['framenameshort']][]            = trim(getid3_lib::iconv_fallback($parsedFrame['encoding'], $info['id3v2']['encoding'], $parsedFrame['data']));
                }
            }
            //unset($parsedFrame['data']); do not unset, may be needed elsewhere, e.g. for replaygain

        } elseif ($parsedFrame['frame_name']{0} == 'T') { // 4.2. T??[?] Text information frame
            //   There may only be one text information frame of its kind in an tag.
            // <Header for 'Text information frame', ID: 'T000' - 'TZZZ',
            // excluding 'TXXX' described in 4.2.6.>
            // Text encoding                $xx
            // Information                  <text string(s) according to encoding>

            $frame_offset = 0;
            $frame_textencoding = ord(substr($parsedFrame['data'], $frame_offset++, 1));
            if ((($id3v2_majorversion <= 3) && ($frame_textencoding > 1)) || (($id3v2_majorversion == 4) && ($frame_textencoding > 3))) {
                $info['warning'][] = 'Invalid text encoding byte (' . $frame_textencoding . ') in frame "' . $parsedFrame['frame_name'] . '" - defaulting to ISO-8859-1 encoding';
            }

            $parsedFrame['data'] = (string) substr($parsedFrame['data'], $frame_offset);

            $parsedFrame['encodingid'] = $frame_textencoding;
            $parsedFrame['encoding']   = $this->TextEncodingNameLookup($frame_textencoding);

            if (!empty($parsedFrame['framenameshort']) && !empty($parsedFrame['data'])) {
                // ID3v2.3 specs say that TPE1 (and others) can contain multiple artist values separated with /
                // This of course breaks when an artist name contains slash character, e.g. "AC/DC"
                // MP3tag (maybe others) implement alternative system where multiple artists are null-separated, which makes more sense
                // getID3 will split null-separated artists into multiple artists and leave slash-separated ones to the user
                switch ($parsedFrame['encoding']) {
                    case 'UTF-16':
                    case 'UTF-16BE':
                    case 'UTF-16LE':
                        $wordsize = 2;
                        break;
                    case 'ISO-8859-1':
                    case 'UTF-8':
                    default:
                        $wordsize = 1;
                        break;
                }
                $Txxx_elements = array();
                $Txxx_elements_start_offset = 0;
                for ($i = 0; $i < strlen($parsedFrame['data']); $i += $wordsize) {
                    if (substr($parsedFrame['data'], $i, $wordsize) == str_repeat("\x00", $wordsize)) {
                        $Txxx_elements[] = substr($parsedFrame['data'], $Txxx_elements_start_offset, $i - $Txxx_elements_start_offset);
                        $Txxx_elements_start_offset = $i + $wordsize;
                    }
                }
                $Txxx_elements[] = substr($parsedFrame['data'], $Txxx_elements_start_offset, $i - $Txxx_elements_start_offset);
                foreach ($Txxx_elements as $Txxx_element) {
                    $string = getid3_lib::iconv_fallback($parsedFrame['encoding'], $info['id3v2']['encoding'], $Txxx_element);
                    if (!empty($string)) {
                        $info['id3v2']['comments'][$parsedFrame['framenameshort']][] = $string;
                    }
                }
                unset($string, $wordsize, $i, $Txxx_elements, $Txxx_element, $Txxx_elements_start_offset);
            }

        } elseif ((($id3v2_majorversion >= 3) && ($parsedFrame['frame_name'] == 'WXXX')) || // 4.3.2 WXXX User defined URL link frame
                (($id3v2_majorversion == 2) && ($parsedFrame['frame_name'] == 'WXX'))) {    // 4.3.2 WXX  User defined URL link frame
            //   There may be more than one 'WXXX' frame in each tag,
            //   but only one with the same description
            // <Header for 'User defined URL link frame', ID: 'WXXX'>
            // Text encoding     $xx
            // Description       <text string according to encoding> $00 (00)
            // URL               <text string>

            $frame_offset = 0;
            $frame_textencoding = ord(substr($parsedFrame['data'], $frame_offset++, 1));
            if ((($id3v2_majorversion <= 3) && ($frame_textencoding > 1)) || (($id3v2_majorversion == 4) && ($frame_textencoding > 3))) {
                $info['warning'][] = 'Invalid text encoding byte (' . $frame_textencoding . ') in frame "' . $parsedFrame['frame_name'] . '" - defaulting to ISO-8859-1 encoding';
            }
            $frame_terminatorpos = strpos($parsedFrame['data'], $this->TextEncodingTerminatorLookup($frame_textencoding), $frame_offset);
            if (ord(substr($parsedFrame['data'], $frame_terminatorpos + strlen($this->TextEncodingTerminatorLookup($frame_textencoding)), 1)) === 0) {
                $frame_terminatorpos++; // strpos() fooled because 2nd byte of Unicode chars are often 0x00
            }
            $frame_description = substr($parsedFrame['data'], $frame_offset, $frame_terminatorpos - $frame_offset);

            if (ord($frame_description) === 0) {
                $frame_description = '';
            }
            $parsedFrame['data'] = substr($parsedFrame['data'], $frame_terminatorpos + strlen($this->TextEncodingTerminatorLookup($frame_textencoding)));

            $frame_terminatorpos = strpos($parsedFrame['data'], $this->TextEncodingTerminatorLookup($frame_textencoding));
            if (ord(substr($parsedFrame['data'], $frame_terminatorpos + strlen($this->TextEncodingTerminatorLookup($frame_textencoding)), 1)) === 0) {
                $frame_terminatorpos++; // strpos() fooled because 2nd byte of Unicode chars are often 0x00
            }
            if ($frame_terminatorpos) {
                // there are null bytes after the data - this is not according to spec
                // only use data up to first null byte
                $frame_urldata = (string) substr($parsedFrame['data'], 0, $frame_terminatorpos);
            } else {
                // no null bytes following data, just use all data
                $frame_urldata = (string) $parsedFrame['data'];
            }

            $parsedFrame['encodingid']  = $frame_textencoding;
            $parsedFrame['encoding']    = $this->TextEncodingNameLookup($frame_textencoding);

            $parsedFrame['url']         = $frame_urldata;
            $parsedFrame['description'] = $frame_description;
            if (!empty($parsedFrame['framenameshort']) && $parsedFrame['url']) {
                $info['id3v2']['comments'][$parsedFrame['framenameshort']][] = getid3_lib::iconv_fallback($parsedFrame['encoding'], $info['id3v2']['encoding'], $parsedFrame['url']);
            }
            unset($parsedFrame['data']);

        } elseif ($parsedFrame['frame_name']{0} == 'W') { // 4.3. W??? URL link frames
            //   There may only be one URL link frame of its kind in a tag,
            //   except when stated otherwise in the frame description
            // <Header for 'URL link frame', ID: 'W000' - 'WZZZ', excluding 'WXXX'
            // described in 4.3.2.>
            // URL              <text string>

            $parsedFrame['url'] = trim($parsedFrame['data']);
            if (!empty($parsedFrame['framenameshort']) && $parsedFrame['url']) {
                $info['id3v2']['comments'][$parsedFrame['framenameshort']][] = $parsedFrame['url'];
            }
            unset($parsedFrame['data']);

        } elseif ((($id3v2_majorversion == 3) && ($parsedFrame['frame_name'] == 'IPLS')) || // 4.4  IPLS Involved people list (ID3v2.3 only)
                (($id3v2_majorversion == 2) && ($parsedFrame['frame_name'] == 'IPL'))) {     // 4.4  IPL  Involved people list (ID3v2.2 only)
            // http://id3.org/id3v2.3.0#sec4.4
            //   There may only be one 'IPL' frame in each tag
            // <Header for 'User defined URL link frame', ID: 'IPL'>
            // Text encoding     $xx
            // People list strings    <textstrings>

            $frame_offset = 0;
            $frame_textencoding = ord(substr($parsedFrame['data'], $frame_offset++, 1));
            if ((($id3v2_majorversion <= 3) && ($frame_textencoding > 1)) || (($id3v2_majorversion == 4) && ($frame_textencoding > 3))) {
                $info['warning'][] = 'Invalid text encoding byte (' . $frame_textencoding . ') in frame "' . $parsedFrame['frame_name'] . '" - defaulting to ISO-8859-1 encoding';
            }
            $parsedFrame['encodingid'] = $frame_textencoding;
            $parsedFrame['encoding']   = $this->TextEncodingNameLookup($parsedFrame['encodingid']);
            $parsedFrame['data_raw']   = (string) substr($parsedFrame['data'], $frame_offset);

            // http://www.getid3.org/phpBB3/viewtopic.php?t=1369
            // "this tag typically contains null terminated strings, which are associated in pairs"
            // "there are users that use the tag incorrectly"
            $IPLS_parts = array();
            if (strpos($parsedFrame['data_raw'], "\x00") !== false) {
                $IPLS_parts_unsorted = array();
                if (((strlen($parsedFrame['data_raw']) % 2) == 0) && ((substr($parsedFrame['data_raw'], 0, 2) == "\xFF\xFE") || (substr($parsedFrame['data_raw'], 0, 2) == "\xFE\xFF"))) {
                    // UTF-16, be careful looking for null bytes since most 2-byte characters may contain one; you need to find twin null bytes, and on even padding
                    $thisILPS  = '';
                    for ($i = 0; $i < strlen($parsedFrame['data_raw']); $i += 2) {
                        $twobytes = substr($parsedFrame['data_raw'], $i, 2);
                        if ($twobytes === "\x00\x00") {
                            $IPLS_parts_unsorted[] = getid3_lib::iconv_fallback($parsedFrame['encoding'], $info['id3v2']['encoding'], $thisILPS);
                            $thisILPS  = '';
                        } else {
                            $thisILPS .= $twobytes;
                        }
                    }
                    if (strlen($thisILPS) > 2) { // 2-byte BOM
                        $IPLS_parts_unsorted[] = getid3_lib::iconv_fallback($parsedFrame['encoding'], $info['id3v2']['encoding'], $thisILPS);
                    }
                } else {
                    // ISO-8859-1 or UTF-8 or other single-byte-null character set
                    $IPLS_parts_unsorted = explode("\x00", $parsedFrame['data_raw']);
                }
                if (count($IPLS_parts_unsorted) == 1) {
                    // just a list of names, e.g. "Dino Baptiste, Jimmy Copley, John Gordon, Bernie Marsden, Sharon Watson"
                    foreach ($IPLS_parts_unsorted as $key => $value) {
                        $IPLS_parts_sorted = preg_split('#[;,\\r\\n\\t]#', $value);
                        $position = '';
                        foreach ($IPLS_parts_sorted as $person) {
                            $IPLS_parts[] = array('position'=>$position, 'person'=>$person);
                        }
                    }
                } elseif ((count($IPLS_parts_unsorted) % 2) == 0) {
                    $position = '';
                    $person   = '';
                    foreach ($IPLS_parts_unsorted as $key => $value) {
                        if (($key % 2) == 0) {
                            $position = $value;
                        } else {
                            $person   = $value;
                            $IPLS_parts[] = array('position'=>$position, 'person'=>$person);
                            $position = '';
                            $person   = '';
                        }
                    }
                } else {
                    foreach ($IPLS_parts_unsorted as $key => $value) {
                        $IPLS_parts[] = array($value);
                    }
                }

            } else {
                $IPLS_parts = preg_split('#[;,\\r\\n\\t]#', $parsedFrame['data_raw']);
            }
            $parsedFrame['data'] = $IPLS_parts;

            if (!empty($parsedFrame['framenameshort']) && !empty($parsedFrame['data'])) {
                $info['id3v2']['comments'][$parsedFrame['framenameshort']][] = $parsedFrame['data'];
            }

        } elseif ((($id3v2_majorversion >= 3) && ($parsedFrame['frame_name'] == 'MCDI')) || // 4.4   MCDI Music CD identifier
                (($id3v2_majorversion == 2) && ($parsedFrame['frame_name'] == 'MCI'))) {     // 4.5   MCI  Music CD identifier
            //   There may only be one 'MCDI' frame in each tag
            // <Header for 'Music CD identifier', ID: 'MCDI'>
            // CD TOC                <binary data>

            if (!empty($parsedFrame['framenameshort']) && !empty($parsedFrame['data'])) {
                $info['id3v2']['comments'][$parsedFrame['framenameshort']][] = $parsedFrame['data'];
            }

        } elseif ((($id3v2_majorversion >= 3) && ($parsedFrame['frame_name'] == 'ETCO')) || // 4.5   ETCO Event timing codes
                (($id3v2_majorversion == 2) && ($parsedFrame['frame_name'] == 'ETC'))) {     // 4.6   ETC  Event timing codes
            //   There may only be one 'ETCO' frame in each tag
            // <Header for 'Event timing codes', ID: 'ETCO'>
            // Time stamp format    $xx
            //   Where time stamp format is:
            // $01  (32-bit value) MPEG frames from beginning of file
            // $02  (32-bit value) milliseconds from beginning of file
            //   Followed by a list of key events in the following format:
            // Type of event   $xx
            // Time stamp      $xx (xx ...)
            //   The 'Time stamp' is set to zero if directly at the beginning of the sound
            //   or after the previous event. All events MUST be sorted in chronological order.

            $frame_offset = 0;
            $parsedFrame['timestampformat'] = ord(substr($parsedFrame['data'], $frame_offset++, 1));

            while ($frame_offset < strlen($parsedFrame['data'])) {
                $parsedFrame['typeid']    = substr($parsedFrame['data'], $frame_offset++, 1);
                $parsedFrame['type']      = $this->ETCOEventLookup($parsedFrame['typeid']);
                $parsedFrame['timestamp'] = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], $frame_offset, 4));
                $frame_offset += 4;
            }
            unset($parsedFrame['data']);

        } elseif ((($id3v2_majorversion >= 3) && ($parsedFrame['frame_name'] == 'MLLT')) || // 4.6   MLLT MPEG location lookup table
                (($id3v2_majorversion == 2) && ($parsedFrame['frame_name'] == 'MLL'))) {     // 4.7   MLL MPEG location lookup table
            //   There may only be one 'MLLT' frame in each tag
            // <Header for 'Location lookup table', ID: 'MLLT'>
            // MPEG frames between reference  $xx xx
            // Bytes between reference        $xx xx xx
            // Milliseconds between reference $xx xx xx
            // Bits for bytes deviation       $xx
            // Bits for milliseconds dev.     $xx
            //   Then for every reference the following data is included;
            // Deviation in bytes         %xxx....
            // Deviation in milliseconds  %xxx....

            $frame_offset = 0;
            $parsedFrame['framesbetweenreferences'] = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], 0, 2));
            $parsedFrame['bytesbetweenreferences']  = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], 2, 3));
            $parsedFrame['msbetweenreferences']     = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], 5, 3));
            $parsedFrame['bitsforbytesdeviation']   = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], 8, 1));
            $parsedFrame['bitsformsdeviation']      = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], 9, 1));
            $parsedFrame['data'] = substr($parsedFrame['data'], 10);
            while ($frame_offset < strlen($parsedFrame['data'])) {
                $deviationbitstream .= getid3_lib::BigEndian2Bin(substr($parsedFrame['data'], $frame_offset++, 1));
            }
            $reference_counter = 0;
            while (strlen($deviationbitstream) > 0) {
                $parsedFrame[$reference_counter]['bytedeviation'] = bindec(substr($deviationbitstream, 0, $parsedFrame['bitsforbytesdeviation']));
                $parsedFrame[$reference_counter]['msdeviation']   = bindec(substr($deviationbitstream, $parsedFrame['bitsforbytesdeviation'], $parsedFrame['bitsformsdeviation']));
                $deviationbitstream = substr($deviationbitstream, $parsedFrame['bitsforbytesdeviation'] + $parsedFrame['bitsformsdeviation']);
                $reference_counter++;
            }
            unset($parsedFrame['data']);

        } elseif ((($id3v2_majorversion >= 3) && ($parsedFrame['frame_name'] == 'SYTC')) || // 4.7   SYTC Synchronised tempo codes
                  (($id3v2_majorversion == 2) && ($parsedFrame['frame_name'] == 'STC'))) {  // 4.8   STC  Synchronised tempo codes
            //   There may only be one 'SYTC' frame in each tag
            // <Header for 'Synchronised tempo codes', ID: 'SYTC'>
            // Time stamp format   $xx
            // Tempo data          <binary data>
            //   Where time stamp format is:
            // $01  (32-bit value) MPEG frames from beginning of file
            // $02  (32-bit value) milliseconds from beginning of file

            $frame_offset = 0;
            $parsedFrame['timestampformat'] = ord(substr($parsedFrame['data'], $frame_offset++, 1));
            $timestamp_counter = 0;
            while ($frame_offset < strlen($parsedFrame['data'])) {
                $parsedFrame[$timestamp_counter]['tempo'] = ord(substr($parsedFrame['data'], $frame_offset++, 1));
                if ($parsedFrame[$timestamp_counter]['tempo'] == 255) {
                    $parsedFrame[$timestamp_counter]['tempo'] += ord(substr($parsedFrame['data'], $frame_offset++, 1));
                }
                $parsedFrame[$timestamp_counter]['timestamp'] = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], $frame_offset, 4));
                $frame_offset += 4;
                $timestamp_counter++;
            }
            unset($parsedFrame['data']);

        } elseif ((($id3v2_majorversion >= 3) && ($parsedFrame['frame_name'] == 'USLT')) || // 4.8   USLT Unsynchronised lyric/text transcription
                (($id3v2_majorversion == 2) && ($parsedFrame['frame_name'] == 'ULT'))) {     // 4.9   ULT  Unsynchronised lyric/text transcription
            //   There may be more than one 'Unsynchronised lyrics/text transcription' frame
            //   in each tag, but only one with the same language and content descriptor.
            // <Header for 'Unsynchronised lyrics/text transcription', ID: 'USLT'>
            // Text encoding        $xx
            // Language             $xx xx xx
            // Content descriptor   <text string according to encoding> $00 (00)
            // Lyrics/text          <full text string according to encoding>

            $frame_offset = 0;
            $frame_textencoding = ord(substr($parsedFrame['data'], $frame_offset++, 1));
            if ((($id3v2_majorversion <= 3) && ($frame_textencoding > 1)) || (($id3v2_majorversion == 4) && ($frame_textencoding > 3))) {
                $info['warning'][] = 'Invalid text encoding byte (' . $frame_textencoding . ') in frame "' . $parsedFrame['frame_name'] . '" - defaulting to ISO-8859-1 encoding';
            }
            $frame_language = substr($parsedFrame['data'], $frame_offset, 3);
            $frame_offset += 3;
            $frame_terminatorpos = strpos($parsedFrame['data'], $this->TextEncodingTerminatorLookup($frame_textencoding), $frame_offset);
            if (ord(substr($parsedFrame['data'], $frame_terminatorpos + strlen($this->TextEncodingTerminatorLookup($frame_textencoding)), 1)) === 0) {
                $frame_terminatorpos++; // strpos() fooled because 2nd byte of Unicode chars are often 0x00
            }
            $frame_description = substr($parsedFrame['data'], $frame_offset, $frame_terminatorpos - $frame_offset);
            if (ord($frame_description) === 0) {
                $frame_description = '';
            }
            $parsedFrame['data'] = substr($parsedFrame['data'], $frame_terminatorpos + strlen($this->TextEncodingTerminatorLookup($frame_textencoding)));

            $parsedFrame['encodingid']   = $frame_textencoding;
            $parsedFrame['encoding']     = $this->TextEncodingNameLookup($frame_textencoding);

            $parsedFrame['data']         = $parsedFrame['data'];
            $parsedFrame['language']     = $frame_language;
            $parsedFrame['languagename'] = $this->LanguageLookup($frame_language, false);
            $parsedFrame['description']  = $frame_description;
            if (!empty($parsedFrame['framenameshort']) && !empty($parsedFrame['data'])) {
                $info['id3v2']['comments'][$parsedFrame['framenameshort']][] = getid3_lib::iconv_fallback($parsedFrame['encoding'], $info['id3v2']['encoding'], $parsedFrame['data']);
            }
            unset($parsedFrame['data']);

        } elseif ((($id3v2_majorversion >= 3) && ($parsedFrame['frame_name'] == 'SYLT')) || // 4.9   SYLT Synchronised lyric/text
                (($id3v2_majorversion == 2) && ($parsedFrame['frame_name'] == 'SLT'))) {     // 4.10  SLT  Synchronised lyric/text
            //   There may be more than one 'SYLT' frame in each tag,
            //   but only one with the same language and content descriptor.
            // <Header for 'Synchronised lyrics/text', ID: 'SYLT'>
            // Text encoding        $xx
            // Language             $xx xx xx
            // Time stamp format    $xx
            //   $01  (32-bit value) MPEG frames from beginning of file
            //   $02  (32-bit value) milliseconds from beginning of file
            // Content type         $xx
            // Content descriptor   <text string according to encoding> $00 (00)
            //   Terminated text to be synced (typically a syllable)
            //   Sync identifier (terminator to above string)   $00 (00)
            //   Time stamp                                     $xx (xx ...)

            $frame_offset = 0;
            $frame_textencoding = ord(substr($parsedFrame['data'], $frame_offset++, 1));
            if ((($id3v2_majorversion <= 3) && ($frame_textencoding > 1)) || (($id3v2_majorversion == 4) && ($frame_textencoding > 3))) {
                $info['warning'][] = 'Invalid text encoding byte (' . $frame_textencoding . ') in frame "' . $parsedFrame['frame_name'] . '" - defaulting to ISO-8859-1 encoding';
            }
            $frame_language = substr($parsedFrame['data'], $frame_offset, 3);
            $frame_offset += 3;
            $parsedFrame['timestampformat'] = ord(substr($parsedFrame['data'], $frame_offset++, 1));
            $parsedFrame['contenttypeid']   = ord(substr($parsedFrame['data'], $frame_offset++, 1));
            $parsedFrame['contenttype']     = $this->SYTLContentTypeLookup($parsedFrame['contenttypeid']);
            $parsedFrame['encodingid']      = $frame_textencoding;
            $parsedFrame['encoding']        = $this->TextEncodingNameLookup($frame_textencoding);

            $parsedFrame['language']        = $frame_language;
            $parsedFrame['languagename']    = $this->LanguageLookup($frame_language, false);

            $timestampindex = 0;
            $frame_remainingdata = substr($parsedFrame['data'], $frame_offset);
            while (strlen($frame_remainingdata)) {
                $frame_offset = 0;
                $frame_terminatorpos = strpos($frame_remainingdata, $this->TextEncodingTerminatorLookup($frame_textencoding));
                if ($frame_terminatorpos === false) {
                    $frame_remainingdata = '';
                } else {
                    if (ord(substr($frame_remainingdata, $frame_terminatorpos + strlen($this->TextEncodingTerminatorLookup($frame_textencoding)), 1)) === 0) {
                        $frame_terminatorpos++; // strpos() fooled because 2nd byte of Unicode chars are often 0x00
                    }
                    $parsedFrame['lyrics'][$timestampindex]['data'] = substr($frame_remainingdata, $frame_offset, $frame_terminatorpos - $frame_offset);

                    $frame_remainingdata = substr($frame_remainingdata, $frame_terminatorpos + strlen($this->TextEncodingTerminatorLookup($frame_textencoding)));
                    if (($timestampindex == 0) && (ord($frame_remainingdata{0}) != 0)) {
                        // timestamp probably omitted for first data item
                    } else {
                        $parsedFrame['lyrics'][$timestampindex]['timestamp'] = getid3_lib::BigEndian2Int(substr($frame_remainingdata, 0, 4));
                        $frame_remainingdata = substr($frame_remainingdata, 4);
                    }
                    $timestampindex++;
                }
            }
            unset($parsedFrame['data']);

        } elseif ((($id3v2_majorversion >= 3) && ($parsedFrame['frame_name'] == 'COMM')) || // 4.10  COMM Comments
                (($id3v2_majorversion == 2) && ($parsedFrame['frame_name'] == 'COM'))) {     // 4.11  COM  Comments
            //   There may be more than one comment frame in each tag,
            //   but only one with the same language and content descriptor.
            // <Header for 'Comment', ID: 'COMM'>
            // Text encoding          $xx
            // Language               $xx xx xx
            // Short content descrip. <text string according to encoding> $00 (00)
            // The actual text        <full text string according to encoding>

            if (strlen($parsedFrame['data']) < 5) {

                $info['warning'][] = 'Invalid data (too short) for "' . $parsedFrame['frame_name'] . '" frame at offset ' . $parsedFrame['dataoffset'];

            } else {

                $frame_offset = 0;
                $frame_textencoding = ord(substr($parsedFrame['data'], $frame_offset++, 1));
                if ((($id3v2_majorversion <= 3) && ($frame_textencoding > 1)) || (($id3v2_majorversion == 4) && ($frame_textencoding > 3))) {
                    $info['warning'][] = 'Invalid text encoding byte (' . $frame_textencoding . ') in frame "' . $parsedFrame['frame_name'] . '" - defaulting to ISO-8859-1 encoding';
                }
                $frame_language = substr($parsedFrame['data'], $frame_offset, 3);
                $frame_offset += 3;
                $frame_terminatorpos = strpos($parsedFrame['data'], $this->TextEncodingTerminatorLookup($frame_textencoding), $frame_offset);
                if (ord(substr($parsedFrame['data'], $frame_terminatorpos + strlen($this->TextEncodingTerminatorLookup($frame_textencoding)), 1)) === 0) {
                    $frame_terminatorpos++; // strpos() fooled because 2nd byte of Unicode chars are often 0x00
                }
                $frame_description = substr($parsedFrame['data'], $frame_offset, $frame_terminatorpos - $frame_offset);
                if (ord($frame_description) === 0) {
                    $frame_description = '';
                }
                $frame_text = (string) substr($parsedFrame['data'], $frame_terminatorpos + strlen($this->TextEncodingTerminatorLookup($frame_textencoding)));

                $parsedFrame['encodingid']   = $frame_textencoding;
                $parsedFrame['encoding']     = $this->TextEncodingNameLookup($frame_textencoding);

                $parsedFrame['language']     = $frame_language;
                $parsedFrame['languagename'] = $this->LanguageLookup($frame_language, false);
                $parsedFrame['description']  = $frame_description;
                $parsedFrame['data']         = $frame_text;
                if (!empty($parsedFrame['framenameshort']) && !empty($parsedFrame['data'])) {
                    $commentkey = ($parsedFrame['description'] ? $parsedFrame['description'] : (!empty($info['id3v2']['comments'][$parsedFrame['framenameshort']]) ? count($info['id3v2']['comments'][$parsedFrame['framenameshort']]) : 0));
                    if (!isset($info['id3v2']['comments'][$parsedFrame['framenameshort']]) || !array_key_exists($commentkey, $info['id3v2']['comments'][$parsedFrame['framenameshort']])) {
                        $info['id3v2']['comments'][$parsedFrame['framenameshort']][$commentkey] = getid3_lib::iconv_fallback($parsedFrame['encoding'], $info['id3v2']['encoding'], $parsedFrame['data']);
                    } else {
                        $info['id3v2']['comments'][$parsedFrame['framenameshort']][]            = getid3_lib::iconv_fallback($parsedFrame['encoding'], $info['id3v2']['encoding'], $parsedFrame['data']);
                    }
                }

            }

        } elseif (($id3v2_majorversion >= 4) && ($parsedFrame['frame_name'] == 'RVA2')) { // 4.11  RVA2 Relative volume adjustment (2) (ID3v2.4+ only)
            //   There may be more than one 'RVA2' frame in each tag,
            //   but only one with the same identification string
            // <Header for 'Relative volume adjustment (2)', ID: 'RVA2'>
            // Identification          <text string> $00
            //   The 'identification' string is used to identify the situation and/or
            //   device where this adjustment should apply. The following is then
            //   repeated for every channel:
            // Type of channel         $xx
            // Volume adjustment       $xx xx
            // Bits representing peak  $xx
            // Peak volume             $xx (xx ...)

            $frame_terminatorpos = strpos($parsedFrame['data'], "\x00");
            $frame_idstring = substr($parsedFrame['data'], 0, $frame_terminatorpos);
            if (ord($frame_idstring) === 0) {
                $frame_idstring = '';
            }
            $frame_remainingdata = substr($parsedFrame['data'], $frame_terminatorpos + strlen("\x00"));
            $parsedFrame['description'] = $frame_idstring;
            $RVA2channelcounter = 0;
            while (strlen($frame_remainingdata) >= 5) {
                $frame_offset = 0;
                $frame_channeltypeid = ord(substr($frame_remainingdata, $frame_offset++, 1));
                $parsedFrame[$RVA2channelcounter]['channeltypeid']  = $frame_channeltypeid;
                $parsedFrame[$RVA2channelcounter]['channeltype']    = $this->RVA2ChannelTypeLookup($frame_channeltypeid);
                $parsedFrame[$RVA2channelcounter]['volumeadjust']   = getid3_lib::BigEndian2Int(substr($frame_remainingdata, $frame_offset, 2), false, true); // 16-bit signed
                $frame_offset += 2;
                $parsedFrame[$RVA2channelcounter]['bitspeakvolume'] = ord(substr($frame_remainingdata, $frame_offset++, 1));
                if (($parsedFrame[$RVA2channelcounter]['bitspeakvolume'] < 1) || ($parsedFrame[$RVA2channelcounter]['bitspeakvolume'] > 4)) {
                    $info['warning'][] = 'ID3v2::RVA2 frame[' . $RVA2channelcounter . '] contains invalid ' . $parsedFrame[$RVA2channelcounter]['bitspeakvolume'] . '-byte bits-representing-peak value';
                    break;
                }
                $frame_bytespeakvolume = ceil($parsedFrame[$RVA2channelcounter]['bitspeakvolume'] / 8);
                $parsedFrame[$RVA2channelcounter]['peakvolume']     = getid3_lib::BigEndian2Int(substr($frame_remainingdata, $frame_offset, $frame_bytespeakvolume));
                $frame_remainingdata = substr($frame_remainingdata, $frame_offset + $frame_bytespeakvolume);
                $RVA2channelcounter++;
            }
            unset($parsedFrame['data']);

        } elseif ((($id3v2_majorversion == 3) && ($parsedFrame['frame_name'] == 'RVAD')) || // 4.12  RVAD Relative volume adjustment (ID3v2.3 only)
                  (($id3v2_majorversion == 2) && ($parsedFrame['frame_name'] == 'RVA'))) {  // 4.12  RVA  Relative volume adjustment (ID3v2.2 only)
            //   There may only be one 'RVA' frame in each tag
            // <Header for 'Relative volume adjustment', ID: 'RVA'>
            // ID3v2.2 => Increment/decrement     %000000ba
            // ID3v2.3 => Increment/decrement     %00fedcba
            // Bits used for volume descr.        $xx
            // Relative volume change, right      $xx xx (xx ...) // a
            // Relative volume change, left       $xx xx (xx ...) // b
            // Peak volume right                  $xx xx (xx ...)
            // Peak volume left                   $xx xx (xx ...)
            //   ID3v2.3 only, optional (not present in ID3v2.2):
            // Relative volume change, right back $xx xx (xx ...) // c
            // Relative volume change, left back  $xx xx (xx ...) // d
            // Peak volume right back             $xx xx (xx ...)
            // Peak volume left back              $xx xx (xx ...)
            //   ID3v2.3 only, optional (not present in ID3v2.2):
            // Relative volume change, center     $xx xx (xx ...) // e
            // Peak volume center                 $xx xx (xx ...)
            //   ID3v2.3 only, optional (not present in ID3v2.2):
            // Relative volume change, bass       $xx xx (xx ...) // f
            // Peak volume bass                   $xx xx (xx ...)

            $frame_offset = 0;
            $frame_incrdecrflags = getid3_lib::BigEndian2Bin(substr($parsedFrame['data'], $frame_offset++, 1));
            $parsedFrame['incdec']['right'] = (bool) substr($frame_incrdecrflags, 6, 1);
            $parsedFrame['incdec']['left']  = (bool) substr($frame_incrdecrflags, 7, 1);
            $parsedFrame['bitsvolume'] = ord(substr($parsedFrame['data'], $frame_offset++, 1));
            $frame_bytesvolume = ceil($parsedFrame['bitsvolume'] / 8);
            $parsedFrame['volumechange']['right'] = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], $frame_offset, $frame_bytesvolume));
            if ($parsedFrame['incdec']['right'] === false) {
                $parsedFrame['volumechange']['right'] *= -1;
            }
            $frame_offset += $frame_bytesvolume;
            $parsedFrame['volumechange']['left'] = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], $frame_offset, $frame_bytesvolume));
            if ($parsedFrame['incdec']['left'] === false) {
                $parsedFrame['volumechange']['left'] *= -1;
            }
            $frame_offset += $frame_bytesvolume;
            $parsedFrame['peakvolume']['right'] = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], $frame_offset, $frame_bytesvolume));
            $frame_offset += $frame_bytesvolume;
            $parsedFrame['peakvolume']['left']  = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], $frame_offset, $frame_bytesvolume));
            $frame_offset += $frame_bytesvolume;
            if ($id3v2_majorversion == 3) {
                $parsedFrame['data'] = substr($parsedFrame['data'], $frame_offset);
                if (strlen($parsedFrame['data']) > 0) {
                    $parsedFrame['incdec']['rightrear'] = (bool) substr($frame_incrdecrflags, 4, 1);
                    $parsedFrame['incdec']['leftrear']  = (bool) substr($frame_incrdecrflags, 5, 1);
                    $parsedFrame['volumechange']['rightrear'] = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], $frame_offset, $frame_bytesvolume));
                    if ($parsedFrame['incdec']['rightrear'] === false) {
                        $parsedFrame['volumechange']['rightrear'] *= -1;
                    }
                    $frame_offset += $frame_bytesvolume;
                    $parsedFrame['volumechange']['leftrear'] = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], $frame_offset, $frame_bytesvolume));
                    if ($parsedFrame['incdec']['leftrear'] === false) {
                        $parsedFrame['volumechange']['leftrear'] *= -1;
                    }
                    $frame_offset += $frame_bytesvolume;
                    $parsedFrame['peakvolume']['rightrear'] = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], $frame_offset, $frame_bytesvolume));
                    $frame_offset += $frame_bytesvolume;
                    $parsedFrame['peakvolume']['leftrear']  = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], $frame_offset, $frame_bytesvolume));
                    $frame_offset += $frame_bytesvolume;
                }
                $parsedFrame['data'] = substr($parsedFrame['data'], $frame_offset);
                if (strlen($parsedFrame['data']) > 0) {
                    $parsedFrame['incdec']['center'] = (bool) substr($frame_incrdecrflags, 3, 1);
                    $parsedFrame['volumechange']['center'] = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], $frame_offset, $frame_bytesvolume));
                    if ($parsedFrame['incdec']['center'] === false) {
                        $parsedFrame['volumechange']['center'] *= -1;
                    }
                    $frame_offset += $frame_bytesvolume;
                    $parsedFrame['peakvolume']['center'] = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], $frame_offset, $frame_bytesvolume));
                    $frame_offset += $frame_bytesvolume;
                }
                $parsedFrame['data'] = substr($parsedFrame['data'], $frame_offset);
                if (strlen($parsedFrame['data']) > 0) {
                    $parsedFrame['incdec']['bass'] = (bool) substr($frame_incrdecrflags, 2, 1);
                    $parsedFrame['volumechange']['bass'] = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], $frame_offset, $frame_bytesvolume));
                    if ($parsedFrame['incdec']['bass'] === false) {
                        $parsedFrame['volumechange']['bass'] *= -1;
                    }
                    $frame_offset += $frame_bytesvolume;
                    $parsedFrame['peakvolume']['bass'] = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], $frame_offset, $frame_bytesvolume));
                    $frame_offset += $frame_bytesvolume;
                }
            }
            unset($parsedFrame['data']);

        } elseif (($id3v2_majorversion >= 4) && ($parsedFrame['frame_name'] == 'EQU2')) { // 4.12  EQU2 Equalisation (2) (ID3v2.4+ only)
            //   There may be more than one 'EQU2' frame in each tag,
            //   but only one with the same identification string
            // <Header of 'Equalisation (2)', ID: 'EQU2'>
            // Interpolation method  $xx
            //   $00  Band
            //   $01  Linear
            // Identification        <text string> $00
            //   The following is then repeated for every adjustment point
            // Frequency          $xx xx
            // Volume adjustment  $xx xx

            $frame_offset = 0;
            $frame_interpolationmethod = ord(substr($parsedFrame['data'], $frame_offset++, 1));
            $frame_terminatorpos = strpos($parsedFrame['data'], "\x00", $frame_offset);
            $frame_idstring = substr($parsedFrame['data'], $frame_offset, $frame_terminatorpos - $frame_offset);
            if (ord($frame_idstring) === 0) {
                $frame_idstring = '';
            }
            $parsedFrame['description'] = $frame_idstring;
            $frame_remainingdata = substr($parsedFrame['data'], $frame_terminatorpos + strlen("\x00"));
            while (strlen($frame_remainingdata)) {
                $frame_frequency = getid3_lib::BigEndian2Int(substr($frame_remainingdata, 0, 2)) / 2;
                $parsedFrame['data'][$frame_frequency] = getid3_lib::BigEndian2Int(substr($frame_remainingdata, 2, 2), false, true);
                $frame_remainingdata = substr($frame_remainingdata, 4);
            }
            $parsedFrame['interpolationmethod'] = $frame_interpolationmethod;
            unset($parsedFrame['data']);

        } elseif ((($id3v2_majorversion == 3) && ($parsedFrame['frame_name'] == 'EQUA')) || // 4.12  EQUA Equalisation (ID3v2.3 only)
                (($id3v2_majorversion == 2) && ($parsedFrame['frame_name'] == 'EQU'))) {     // 4.13  EQU  Equalisation (ID3v2.2 only)
            //   There may only be one 'EQUA' frame in each tag
            // <Header for 'Relative volume adjustment', ID: 'EQU'>
            // Adjustment bits    $xx
            //   This is followed by 2 bytes + ('adjustment bits' rounded up to the
            //   nearest byte) for every equalisation band in the following format,
            //   giving a frequency range of 0 - 32767Hz:
            // Increment/decrement   %x (MSB of the Frequency)
            // Frequency             (lower 15 bits)
            // Adjustment            $xx (xx ...)

            $frame_offset = 0;
            $parsedFrame['adjustmentbits'] = substr($parsedFrame['data'], $frame_offset++, 1);
            $frame_adjustmentbytes = ceil($parsedFrame['adjustmentbits'] / 8);

            $frame_remainingdata = (string) substr($parsedFrame['data'], $frame_offset);
            while (strlen($frame_remainingdata) > 0) {
                $frame_frequencystr = getid3_lib::BigEndian2Bin(substr($frame_remainingdata, 0, 2));
                $frame_incdec    = (bool) substr($frame_frequencystr, 0, 1);
                $frame_frequency = bindec(substr($frame_frequencystr, 1, 15));
                $parsedFrame[$frame_frequency]['incdec'] = $frame_incdec;
                $parsedFrame[$frame_frequency]['adjustment'] = getid3_lib::BigEndian2Int(substr($frame_remainingdata, 2, $frame_adjustmentbytes));
                if ($parsedFrame[$frame_frequency]['incdec'] === false) {
                    $parsedFrame[$frame_frequency]['adjustment'] *= -1;
                }
                $frame_remainingdata = substr($frame_remainingdata, 2 + $frame_adjustmentbytes);
            }
            unset($parsedFrame['data']);

        } elseif ((($id3v2_majorversion >= 3) && ($parsedFrame['frame_name'] == 'RVRB')) || // 4.13  RVRB Reverb
                (($id3v2_majorversion == 2) && ($parsedFrame['frame_name'] == 'REV'))) {     // 4.14  REV  Reverb
            //   There may only be one 'RVRB' frame in each tag.
            // <Header for 'Reverb', ID: 'RVRB'>
            // Reverb left (ms)                 $xx xx
            // Reverb right (ms)                $xx xx
            // Reverb bounces, left             $xx
            // Reverb bounces, right            $xx
            // Reverb feedback, left to left    $xx
            // Reverb feedback, left to right   $xx
            // Reverb feedback, right to right  $xx
            // Reverb feedback, right to left   $xx
            // Premix left to right             $xx
            // Premix right to left             $xx

            $frame_offset = 0;
            $parsedFrame['left']  = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], $frame_offset, 2));
            $frame_offset += 2;
            $parsedFrame['right'] = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], $frame_offset, 2));
            $frame_offset += 2;
            $parsedFrame['bouncesL']      = ord(substr($parsedFrame['data'], $frame_offset++, 1));
            $parsedFrame['bouncesR']      = ord(substr($parsedFrame['data'], $frame_offset++, 1));
            $parsedFrame['feedbackLL']    = ord(substr($parsedFrame['data'], $frame_offset++, 1));
            $parsedFrame['feedbackLR']    = ord(substr($parsedFrame['data'], $frame_offset++, 1));
            $parsedFrame['feedbackRR']    = ord(substr($parsedFrame['data'], $frame_offset++, 1));
            $parsedFrame['feedbackRL']    = ord(substr($parsedFrame['data'], $frame_offset++, 1));
            $parsedFrame['premixLR']      = ord(substr($parsedFrame['data'], $frame_offset++, 1));
            $parsedFrame['premixRL']      = ord(substr($parsedFrame['data'], $frame_offset++, 1));
            unset($parsedFrame['data']);

        } elseif ((($id3v2_majorversion >= 3) && ($parsedFrame['frame_name'] == 'APIC')) || // 4.14  APIC Attached picture
                (($id3v2_majorversion == 2) && ($parsedFrame['frame_name'] == 'PIC'))) {     // 4.15  PIC  Attached picture
            //   There may be several pictures attached to one file,
            //   each in their individual 'APIC' frame, but only one
            //   with the same content descriptor
            // <Header for 'Attached picture', ID: 'APIC'>
            // Text encoding      $xx
            // ID3v2.3+ => MIME type          <text string> $00
            // ID3v2.2  => Image format       $xx xx xx
            // Picture type       $xx
            // Description        <text string according to encoding> $00 (00)
            // Picture data       <binary data>

            $frame_offset = 0;
            $frame_textencoding = ord(substr($parsedFrame['data'], $frame_offset++, 1));
            if ((($id3v2_majorversion <= 3) && ($frame_textencoding > 1)) || (($id3v2_majorversion == 4) && ($frame_textencoding > 3))) {
                $info['warning'][] = 'Invalid text encoding byte (' . $frame_textencoding . ') in frame "' . $parsedFrame['frame_name'] . '" - defaulting to ISO-8859-1 encoding';
            }

            if ($id3v2_majorversion == 2 && strlen($parsedFrame['data']) > $frame_offset) {
                $frame_imagetype = substr($parsedFrame['data'], $frame_offset, 3);
                if (strtolower($frame_imagetype) == 'ima') {
                    // complete hack for mp3Rage (www.chaoticsoftware.com) that puts ID3v2.3-formatted
                    // MIME type instead of 3-char ID3v2.2-format image type  (thanks xbhoffØpacbell*net)
                    $frame_terminatorpos = strpos($parsedFrame['data'], "\x00", $frame_offset);
                    $frame_mimetype = substr($parsedFrame['data'], $frame_offset, $frame_terminatorpos - $frame_offset);
                    if (ord($frame_mimetype) === 0) {
                        $frame_mimetype = '';
                    }
                    $frame_imagetype = strtoupper(str_replace('image/', '', strtolower($frame_mimetype)));
                    if ($frame_imagetype == 'JPEG') {
                        $frame_imagetype = 'JPG';
                    }
                    $frame_offset = $frame_terminatorpos + strlen("\x00");
                } else {
                    $frame_offset += 3;
                }
            }
            if ($id3v2_majorversion > 2 && strlen($parsedFrame['data']) > $frame_offset) {
                $frame_terminatorpos = strpos($parsedFrame['data'], "\x00", $frame_offset);
                $frame_mimetype = substr($parsedFrame['data'], $frame_offset, $frame_terminatorpos - $frame_offset);
                if (ord($frame_mimetype) === 0) {
                    $frame_mimetype = '';
                }
                $frame_offset = $frame_terminatorpos + strlen("\x00");
            }

            $frame_picturetype = ord(substr($parsedFrame['data'], $frame_offset++, 1));

            if ($frame_offset >= $parsedFrame['datalength']) {
                $info['warning'][] = 'data portion of APIC frame is missing at offset ' . ($parsedFrame['dataoffset'] + 8 + $frame_offset);
            } else {
                $frame_terminatorpos = strpos($parsedFrame['data'], $this->TextEncodingTerminatorLookup($frame_textencoding), $frame_offset);
                if (ord(substr($parsedFrame['data'], $frame_terminatorpos + strlen($this->TextEncodingTerminatorLookup($frame_textencoding)), 1)) === 0) {
                    $frame_terminatorpos++; // strpos() fooled because 2nd byte of Unicode chars are often 0x00
                }
                $frame_description = substr($parsedFrame['data'], $frame_offset, $frame_terminatorpos - $frame_offset);
                if (ord($frame_description) === 0) {
                    $frame_description = '';
                }
                $parsedFrame['encodingid']       = $frame_textencoding;
                $parsedFrame['encoding']         = $this->TextEncodingNameLookup($frame_textencoding);

                if ($id3v2_majorversion == 2) {
                    $parsedFrame['imagetype']    = $frame_imagetype;
                } else {
                    $parsedFrame['mime']         = $frame_mimetype;
                }
                $parsedFrame['picturetypeid']    = $frame_picturetype;
                $parsedFrame['picturetype']      = $this->APICPictureTypeLookup($frame_picturetype);
                $parsedFrame['description']      = $frame_description;
                $parsedFrame['data']             = substr($parsedFrame['data'], $frame_terminatorpos + strlen($this->TextEncodingTerminatorLookup($frame_textencoding)));
                $parsedFrame['datalength']       = strlen($parsedFrame['data']);

                $parsedFrame['image_mime'] = '';
                $imageinfo = array();
                $imagechunkcheck = getid3_lib::GetDataImageSize($parsedFrame['data'], $imageinfo);
                if (($imagechunkcheck[2] >= 1) && ($imagechunkcheck[2] <= 3)) {
                    $parsedFrame['image_mime']       = 'image/' . getid3_lib::ImageTypesLookup($imagechunkcheck[2]);
                    if ($imagechunkcheck[0]) {
                        $parsedFrame['image_width']  = $imagechunkcheck[0];
                    }
                    if ($imagechunkcheck[1]) {
                        $parsedFrame['image_height'] = $imagechunkcheck[1];
                    }
                }

                do {
                    if ($this->getid3->option_save_attachments === false) {
                        // skip entirely
                        unset($parsedFrame['data']);
                        break;
                    }
                    if ($this->getid3->option_save_attachments === true) {
                        // great
/*
                    } elseif (is_int($this->getid3->option_save_attachments)) {
                        if ($this->getid3->option_save_attachments < $parsedFrame['data_length']) {
                            // too big, skip
                            $info['warning'][] = 'attachment at '.$frame_offset.' is too large to process inline ('.number_format($parsedFrame['data_length']).' bytes)';
                            unset($parsedFrame['data']);
                            break;
                        }
*/
                    } elseif (is_string($this->getid3->option_save_attachments)) {
                        $dir = rtrim(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $this->getid3->option_save_attachments), DIRECTORY_SEPARATOR);
                        if (!is_dir($dir) || !is_writable($dir)) {
                            // cannot write, skip
                            $info['warning'][] = 'attachment at ' . $frame_offset . ' cannot be saved to "' . $dir . '" (not writable)';
                            unset($parsedFrame['data']);
                            break;
                        }
                    }
                    // if we get this far, must be OK
                    if (is_string($this->getid3->option_save_attachments)) {
                        $destination_filename = $dir . DIRECTORY_SEPARATOR . md5($info['filenamepath']) . '_' . $frame_offset;
                        if (!file_exists($destination_filename) || is_writable($destination_filename)) {
                            file_put_contents($destination_filename, $parsedFrame['data']);
                        } else {
                            $info['warning'][] = 'attachment at ' . $frame_offset . ' cannot be saved to "' . $destination_filename . '" (not writable)';
                        }
                        $parsedFrame['data_filename'] = $destination_filename;
                        unset($parsedFrame['data']);
                    } else {
                        if (!empty($parsedFrame['framenameshort']) && !empty($parsedFrame['data'])) {
                            if (!isset($info['id3v2']['comments']['picture'])) {
                                $info['id3v2']['comments']['picture'] = array();
                            }
                            $info['id3v2']['comments']['picture'][] = array('data'=>$parsedFrame['data'], 'image_mime'=>$parsedFrame['image_mime']);
                        }
                    }
                } while (false);
            }

        } elseif ((($id3v2_majorversion >= 3) && ($parsedFrame['frame_name'] == 'GEOB')) || // 4.15  GEOB General encapsulated object
                (($id3v2_majorversion == 2) && ($parsedFrame['frame_name'] == 'GEO'))) {     // 4.16  GEO  General encapsulated object
            //   There may be more than one 'GEOB' frame in each tag,
            //   but only one with the same content descriptor
            // <Header for 'General encapsulated object', ID: 'GEOB'>
            // Text encoding          $xx
            // MIME type              <text string> $00
            // Filename               <text string according to encoding> $00 (00)
            // Content description    <text string according to encoding> $00 (00)
            // Encapsulated object    <binary data>

            $frame_offset = 0;
            $frame_textencoding = ord(substr($parsedFrame['data'], $frame_offset++, 1));
            if ((($id3v2_majorversion <= 3) && ($frame_textencoding > 1)) || (($id3v2_majorversion == 4) && ($frame_textencoding > 3))) {
                $info['warning'][] = 'Invalid text encoding byte (' . $frame_textencoding . ') in frame "' . $parsedFrame['frame_name'] . '" - defaulting to ISO-8859-1 encoding';
            }
            $frame_terminatorpos = strpos($parsedFrame['data'], "\x00", $frame_offset);
            $frame_mimetype = substr($parsedFrame['data'], $frame_offset, $frame_terminatorpos - $frame_offset);
            if (ord($frame_mimetype) === 0) {
                $frame_mimetype = '';
            }
            $frame_offset = $frame_terminatorpos + strlen("\x00");

            $frame_terminatorpos = strpos($parsedFrame['data'], $this->TextEncodingTerminatorLookup($frame_textencoding), $frame_offset);
            if (ord(substr($parsedFrame['data'], $frame_terminatorpos + strlen($this->TextEncodingTerminatorLookup($frame_textencoding)), 1)) === 0) {
                $frame_terminatorpos++; // strpos() fooled because 2nd byte of Unicode chars are often 0x00
            }
            $frame_filename = substr($parsedFrame['data'], $frame_offset, $frame_terminatorpos - $frame_offset);
            if (ord($frame_filename) === 0) {
                $frame_filename = '';
            }
            $frame_offset = $frame_terminatorpos + strlen($this->TextEncodingTerminatorLookup($frame_textencoding));

            $frame_terminatorpos = strpos($parsedFrame['data'], $this->TextEncodingTerminatorLookup($frame_textencoding), $frame_offset);
            if (ord(substr($parsedFrame['data'], $frame_terminatorpos + strlen($this->TextEncodingTerminatorLookup($frame_textencoding)), 1)) === 0) {
                $frame_terminatorpos++; // strpos() fooled because 2nd byte of Unicode chars are often 0x00
            }
            $frame_description = substr($parsedFrame['data'], $frame_offset, $frame_terminatorpos - $frame_offset);
            if (ord($frame_description) === 0) {
                $frame_description = '';
            }
            $frame_offset = $frame_terminatorpos + strlen($this->TextEncodingTerminatorLookup($frame_textencoding));

            $parsedFrame['objectdata']  = (string) substr($parsedFrame['data'], $frame_offset);
            $parsedFrame['encodingid']  = $frame_textencoding;
            $parsedFrame['encoding']    = $this->TextEncodingNameLookup($frame_textencoding);

            $parsedFrame['mime']        = $frame_mimetype;
            $parsedFrame['filename']    = $frame_filename;
            $parsedFrame['description'] = $frame_description;
            unset($parsedFrame['data']);

        } elseif ((($id3v2_majorversion >= 3) && ($parsedFrame['frame_name'] == 'PCNT')) || // 4.16  PCNT Play counter
                (($id3v2_majorversion == 2) && ($parsedFrame['frame_name'] == 'CNT'))) {     // 4.17  CNT  Play counter
            //   There may only be one 'PCNT' frame in each tag.
            //   When the counter reaches all one's, one byte is inserted in
            //   front of the counter thus making the counter eight bits bigger
            // <Header for 'Play counter', ID: 'PCNT'>
            // Counter        $xx xx xx xx (xx ...)

            $parsedFrame['data']          = getid3_lib::BigEndian2Int($parsedFrame['data']);

        } elseif ((($id3v2_majorversion >= 3) && ($parsedFrame['frame_name'] == 'POPM')) || // 4.17  POPM Popularimeter
                (($id3v2_majorversion == 2) && ($parsedFrame['frame_name'] == 'POP'))) {    // 4.18  POP  Popularimeter
            //   There may be more than one 'POPM' frame in each tag,
            //   but only one with the same email address
            // <Header for 'Popularimeter', ID: 'POPM'>
            // Email to user   <text string> $00
            // Rating          $xx
            // Counter         $xx xx xx xx (xx ...)

            $frame_offset = 0;
            $frame_terminatorpos = strpos($parsedFrame['data'], "\x00", $frame_offset);
            $frame_emailaddress = substr($parsedFrame['data'], $frame_offset, $frame_terminatorpos - $frame_offset);
            if (ord($frame_emailaddress) === 0) {
                $frame_emailaddress = '';
            }
            $frame_offset = $frame_terminatorpos + strlen("\x00");
            $frame_rating = ord(substr($parsedFrame['data'], $frame_offset++, 1));
            $parsedFrame['counter'] = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], $frame_offset));
            $parsedFrame['email']   = $frame_emailaddress;
            $parsedFrame['rating']  = $frame_rating;
            unset($parsedFrame['data']);

        } elseif ((($id3v2_majorversion >= 3) && ($parsedFrame['frame_name'] == 'RBUF')) || // 4.18  RBUF Recommended buffer size
                (($id3v2_majorversion == 2) && ($parsedFrame['frame_name'] == 'BUF'))) {     // 4.19  BUF  Recommended buffer size
            //   There may only be one 'RBUF' frame in each tag
            // <Header for 'Recommended buffer size', ID: 'RBUF'>
            // Buffer size               $xx xx xx
            // Embedded info flag        %0000000x
            // Offset to next tag        $xx xx xx xx

            $frame_offset = 0;
            $parsedFrame['buffersize'] = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], $frame_offset, 3));
            $frame_offset += 3;

            $frame_embeddedinfoflags = getid3_lib::BigEndian2Bin(substr($parsedFrame['data'], $frame_offset++, 1));
            $parsedFrame['flags']['embededinfo'] = (bool) substr($frame_embeddedinfoflags, 7, 1);
            $parsedFrame['nexttagoffset'] = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], $frame_offset, 4));
            unset($parsedFrame['data']);

        } elseif (($id3v2_majorversion == 2) && ($parsedFrame['frame_name'] == 'CRM')) { // 4.20  Encrypted meta frame (ID3v2.2 only)
            //   There may be more than one 'CRM' frame in a tag,
            //   but only one with the same 'owner identifier'
            // <Header for 'Encrypted meta frame', ID: 'CRM'>
            // Owner identifier      <textstring> $00 (00)
            // Content/explanation   <textstring> $00 (00)
            // Encrypted datablock   <binary data>

            $frame_offset = 0;
            $frame_terminatorpos = strpos($parsedFrame['data'], "\x00", $frame_offset);
            $frame_ownerid = substr($parsedFrame['data'], $frame_offset, $frame_terminatorpos - $frame_offset);
            $frame_offset = $frame_terminatorpos + strlen("\x00");

            $frame_terminatorpos = strpos($parsedFrame['data'], "\x00", $frame_offset);
            $frame_description = substr($parsedFrame['data'], $frame_offset, $frame_terminatorpos - $frame_offset);
            if (ord($frame_description) === 0) {
                $frame_description = '';
            }
            $frame_offset = $frame_terminatorpos + strlen("\x00");

            $parsedFrame['ownerid']     = $frame_ownerid;
            $parsedFrame['data']        = (string) substr($parsedFrame['data'], $frame_offset);
            $parsedFrame['description'] = $frame_description;
            unset($parsedFrame['data']);

        } elseif ((($id3v2_majorversion >= 3) && ($parsedFrame['frame_name'] == 'AENC')) || // 4.19  AENC Audio encryption
                (($id3v2_majorversion == 2) && ($parsedFrame['frame_name'] == 'CRA'))) {     // 4.21  CRA  Audio encryption
            //   There may be more than one 'AENC' frames in a tag,
            //   but only one with the same 'Owner identifier'
            // <Header for 'Audio encryption', ID: 'AENC'>
            // Owner identifier   <text string> $00
            // Preview start      $xx xx
            // Preview length     $xx xx
            // Encryption info    <binary data>

            $frame_offset = 0;
            $frame_terminatorpos = strpos($parsedFrame['data'], "\x00", $frame_offset);
            $frame_ownerid = substr($parsedFrame['data'], $frame_offset, $frame_terminatorpos - $frame_offset);
            if (ord($frame_ownerid) === 0) {
                $frame_ownerid == '';
            }
            $frame_offset = $frame_terminatorpos + strlen("\x00");
            $parsedFrame['ownerid'] = $frame_ownerid;
            $parsedFrame['previewstart'] = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], $frame_offset, 2));
            $frame_offset += 2;
            $parsedFrame['previewlength'] = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], $frame_offset, 2));
            $frame_offset += 2;
            $parsedFrame['encryptioninfo'] = (string) substr($parsedFrame['data'], $frame_offset);
            unset($parsedFrame['data']);

        } elseif ((($id3v2_majorversion >= 3) && ($parsedFrame['frame_name'] == 'LINK')) || // 4.20  LINK Linked information
                (($id3v2_majorversion == 2) && ($parsedFrame['frame_name'] == 'LNK'))) {     // 4.22  LNK  Linked information
            //   There may be more than one 'LINK' frame in a tag,
            //   but only one with the same contents
            // <Header for 'Linked information', ID: 'LINK'>
            // ID3v2.3+ => Frame identifier   $xx xx xx xx
            // ID3v2.2  => Frame identifier   $xx xx xx
            // URL                            <text string> $00
            // ID and additional data         <text string(s)>

            $frame_offset = 0;
            if ($id3v2_majorversion == 2) {
                $parsedFrame['frameid'] = substr($parsedFrame['data'], $frame_offset, 3);
                $frame_offset += 3;
            } else {
                $parsedFrame['frameid'] = substr($parsedFrame['data'], $frame_offset, 4);
                $frame_offset += 4;
            }

            $frame_terminatorpos = strpos($parsedFrame['data'], "\x00", $frame_offset);
            $frame_url = substr($parsedFrame['data'], $frame_offset, $frame_terminatorpos - $frame_offset);
            if (ord($frame_url) === 0) {
                $frame_url = '';
            }
            $frame_offset = $frame_terminatorpos + strlen("\x00");
            $parsedFrame['url'] = $frame_url;

            $parsedFrame['additionaldata'] = (string) substr($parsedFrame['data'], $frame_offset);
            if (!empty($parsedFrame['framenameshort']) && $parsedFrame['url']) {
                $info['id3v2']['comments'][$parsedFrame['framenameshort']][] = utf8_encode($parsedFrame['url']);
            }
            unset($parsedFrame['data']);

        } elseif (($id3v2_majorversion >= 3) && ($parsedFrame['frame_name'] == 'POSS')) { // 4.21  POSS Position synchronisation frame (ID3v2.3+ only)
            //   There may only be one 'POSS' frame in each tag
            // <Head for 'Position synchronisation', ID: 'POSS'>
            // Time stamp format         $xx
            // Position                  $xx (xx ...)

            $frame_offset = 0;
            $parsedFrame['timestampformat'] = ord(substr($parsedFrame['data'], $frame_offset++, 1));
            $parsedFrame['position']        = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], $frame_offset));
            unset($parsedFrame['data']);

        } elseif (($id3v2_majorversion >= 3) && ($parsedFrame['frame_name'] == 'USER')) { // 4.22  USER Terms of use (ID3v2.3+ only)
            //   There may be more than one 'Terms of use' frame in a tag,
            //   but only one with the same 'Language'
            // <Header for 'Terms of use frame', ID: 'USER'>
            // Text encoding        $xx
            // Language             $xx xx xx
            // The actual text      <text string according to encoding>

            $frame_offset = 0;
            $frame_textencoding = ord(substr($parsedFrame['data'], $frame_offset++, 1));
            if ((($id3v2_majorversion <= 3) && ($frame_textencoding > 1)) || (($id3v2_majorversion == 4) && ($frame_textencoding > 3))) {
                $info['warning'][] = 'Invalid text encoding byte (' . $frame_textencoding . ') in frame "' . $parsedFrame['frame_name'] . '" - defaulting to ISO-8859-1 encoding';
            }
            $frame_language = substr($parsedFrame['data'], $frame_offset, 3);
            $frame_offset += 3;
            $parsedFrame['language']     = $frame_language;
            $parsedFrame['languagename'] = $this->LanguageLookup($frame_language, false);
            $parsedFrame['encodingid']   = $frame_textencoding;
            $parsedFrame['encoding']     = $this->TextEncodingNameLookup($frame_textencoding);

            $parsedFrame['data']         = (string) substr($parsedFrame['data'], $frame_offset);
            if (!empty($parsedFrame['framenameshort']) && !empty($parsedFrame['data'])) {
                $info['id3v2']['comments'][$parsedFrame['framenameshort']][] = getid3_lib::iconv_fallback($parsedFrame['encoding'], $info['id3v2']['encoding'], $parsedFrame['data']);
            }
            unset($parsedFrame['data']);

        } elseif (($id3v2_majorversion >= 3) && ($parsedFrame['frame_name'] == 'OWNE')) { // 4.23  OWNE Ownership frame (ID3v2.3+ only)
            //   There may only be one 'OWNE' frame in a tag
            // <Header for 'Ownership frame', ID: 'OWNE'>
            // Text encoding     $xx
            // Price paid        <text string> $00
            // Date of purch.    <text string>
            // Seller            <text string according to encoding>

            $frame_offset = 0;
            $frame_textencoding = ord(substr($parsedFrame['data'], $frame_offset++, 1));
            if ((($id3v2_majorversion <= 3) && ($frame_textencoding > 1)) || (($id3v2_majorversion == 4) && ($frame_textencoding > 3))) {
                $info['warning'][] = 'Invalid text encoding byte (' . $frame_textencoding . ') in frame "' . $parsedFrame['frame_name'] . '" - defaulting to ISO-8859-1 encoding';
            }
            $parsedFrame['encodingid'] = $frame_textencoding;
            $parsedFrame['encoding']   = $this->TextEncodingNameLookup($frame_textencoding);

            $frame_terminatorpos = strpos($parsedFrame['data'], "\x00", $frame_offset);
            $frame_pricepaid = substr($parsedFrame['data'], $frame_offset, $frame_terminatorpos - $frame_offset);
            $frame_offset = $frame_terminatorpos + strlen("\x00");

            $parsedFrame['pricepaid']['currencyid'] = substr($frame_pricepaid, 0, 3);
            $parsedFrame['pricepaid']['currency']   = $this->LookupCurrencyUnits($parsedFrame['pricepaid']['currencyid']);
            $parsedFrame['pricepaid']['value']      = substr($frame_pricepaid, 3);

            $parsedFrame['purchasedate'] = substr($parsedFrame['data'], $frame_offset, 8);
            if (!$this->IsValidDateStampString($parsedFrame['purchasedate'])) {
                $parsedFrame['purchasedateunix'] = mktime(0, 0, 0, substr($parsedFrame['purchasedate'], 4, 2), substr($parsedFrame['purchasedate'], 6, 2), substr($parsedFrame['purchasedate'], 0, 4));
            }
            $frame_offset += 8;

            $parsedFrame['seller'] = (string) substr($parsedFrame['data'], $frame_offset);
            unset($parsedFrame['data']);

        } elseif (($id3v2_majorversion >= 3) && ($parsedFrame['frame_name'] == 'COMR')) { // 4.24  COMR Commercial frame (ID3v2.3+ only)
            //   There may be more than one 'commercial frame' in a tag,
            //   but no two may be identical
            // <Header for 'Commercial frame', ID: 'COMR'>
            // Text encoding      $xx
            // Price string       <text string> $00
            // Valid until        <text string>
            // Contact URL        <text string> $00
            // Received as        $xx
            // Name of seller     <text string according to encoding> $00 (00)
            // Description        <text string according to encoding> $00 (00)
            // Picture MIME type  <string> $00
            // Seller logo        <binary data>

            $frame_offset = 0;
            $frame_textencoding = ord(substr($parsedFrame['data'], $frame_offset++, 1));
            if ((($id3v2_majorversion <= 3) && ($frame_textencoding > 1)) || (($id3v2_majorversion == 4) && ($frame_textencoding > 3))) {
                $info['warning'][] = 'Invalid text encoding byte (' . $frame_textencoding . ') in frame "' . $parsedFrame['frame_name'] . '" - defaulting to ISO-8859-1 encoding';
            }

            $frame_terminatorpos = strpos($parsedFrame['data'], "\x00", $frame_offset);
            $frame_pricestring = substr($parsedFrame['data'], $frame_offset, $frame_terminatorpos - $frame_offset);
            $frame_offset = $frame_terminatorpos + strlen("\x00");
            $frame_rawpricearray = explode('/', $frame_pricestring);
            foreach ($frame_rawpricearray as $key => $val) {
                $frame_currencyid = substr($val, 0, 3);
                $parsedFrame['price'][$frame_currencyid]['currency'] = $this->LookupCurrencyUnits($frame_currencyid);
                $parsedFrame['price'][$frame_currencyid]['value']    = substr($val, 3);
            }

            $frame_datestring = substr($parsedFrame['data'], $frame_offset, 8);
            $frame_offset += 8;

            $frame_terminatorpos = strpos($parsedFrame['data'], "\x00", $frame_offset);
            $frame_contacturl = substr($parsedFrame['data'], $frame_offset, $frame_terminatorpos - $frame_offset);
            $frame_offset = $frame_terminatorpos + strlen("\x00");

            $frame_receivedasid = ord(substr($parsedFrame['data'], $frame_offset++, 1));

            $frame_terminatorpos = strpos($parsedFrame['data'], $this->TextEncodingTerminatorLookup($frame_textencoding), $frame_offset);
            if (ord(substr($parsedFrame['data'], $frame_terminatorpos + strlen($this->TextEncodingTerminatorLookup($frame_textencoding)), 1)) === 0) {
                $frame_terminatorpos++; // strpos() fooled because 2nd byte of Unicode chars are often 0x00
            }
            $frame_sellername = substr($parsedFrame['data'], $frame_offset, $frame_terminatorpos - $frame_offset);
            if (ord($frame_sellername) === 0) {
                $frame_sellername = '';
            }
            $frame_offset = $frame_terminatorpos + strlen($this->TextEncodingTerminatorLookup($frame_textencoding));

            $frame_terminatorpos = strpos($parsedFrame['data'], $this->TextEncodingTerminatorLookup($frame_textencoding), $frame_offset);
            if (ord(substr($parsedFrame['data'], $frame_terminatorpos + strlen($this->TextEncodingTerminatorLookup($frame_textencoding)), 1)) === 0) {
                $frame_terminatorpos++; // strpos() fooled because 2nd byte of Unicode chars are often 0x00
            }
            $frame_description = substr($parsedFrame['data'], $frame_offset, $frame_terminatorpos - $frame_offset);
            if (ord($frame_description) === 0) {
                $frame_description = '';
            }
            $frame_offset = $frame_terminatorpos + strlen($this->TextEncodingTerminatorLookup($frame_textencoding));

            $frame_terminatorpos = strpos($parsedFrame['data'], "\x00", $frame_offset);
            $frame_mimetype = substr($parsedFrame['data'], $frame_offset, $frame_terminatorpos - $frame_offset);
            $frame_offset = $frame_terminatorpos + strlen("\x00");

            $frame_sellerlogo = substr($parsedFrame['data'], $frame_offset);

            $parsedFrame['encodingid']        = $frame_textencoding;
            $parsedFrame['encoding']          = $this->TextEncodingNameLookup($frame_textencoding);

            $parsedFrame['pricevaliduntil']   = $frame_datestring;
            $parsedFrame['contacturl']        = $frame_contacturl;
            $parsedFrame['receivedasid']      = $frame_receivedasid;
            $parsedFrame['receivedas']        = $this->COMRReceivedAsLookup($frame_receivedasid);
            $parsedFrame['sellername']        = $frame_sellername;
            $parsedFrame['description']       = $frame_description;
            $parsedFrame['mime']              = $frame_mimetype;
            $parsedFrame['logo']              = $frame_sellerlogo;
            unset($parsedFrame['data']);

        } elseif (($id3v2_majorversion >= 3) && ($parsedFrame['frame_name'] == 'ENCR')) { // 4.25  ENCR Encryption method registration (ID3v2.3+ only)
            //   There may be several 'ENCR' frames in a tag,
            //   but only one containing the same symbol
            //   and only one containing the same owner identifier
            // <Header for 'Encryption method registration', ID: 'ENCR'>
            // Owner identifier    <text string> $00
            // Method symbol       $xx
            // Encryption data     <binary data>

            $frame_offset = 0;
            $frame_terminatorpos = strpos($parsedFrame['data'], "\x00", $frame_offset);
            $frame_ownerid = substr($parsedFrame['data'], $frame_offset, $frame_terminatorpos - $frame_offset);
            if (ord($frame_ownerid) === 0) {
                $frame_ownerid = '';
            }
            $frame_offset = $frame_terminatorpos + strlen("\x00");

            $parsedFrame['ownerid']      = $frame_ownerid;
            $parsedFrame['methodsymbol'] = ord(substr($parsedFrame['data'], $frame_offset++, 1));
            $parsedFrame['data']         = (string) substr($parsedFrame['data'], $frame_offset);

        } elseif (($id3v2_majorversion >= 3) && ($parsedFrame['frame_name'] == 'GRID')) { // 4.26  GRID Group identification registration (ID3v2.3+ only)

            //   There may be several 'GRID' frames in a tag,
            //   but only one containing the same symbol
            //   and only one containing the same owner identifier
            // <Header for 'Group ID registration', ID: 'GRID'>
            // Owner identifier      <text string> $00
            // Group symbol          $xx
            // Group dependent data  <binary data>

            $frame_offset = 0;
            $frame_terminatorpos = strpos($parsedFrame['data'], "\x00", $frame_offset);
            $frame_ownerid = substr($parsedFrame['data'], $frame_offset, $frame_terminatorpos - $frame_offset);
            if (ord($frame_ownerid) === 0) {
                $frame_ownerid = '';
            }
            $frame_offset = $frame_terminatorpos + strlen("\x00");

            $parsedFrame['ownerid']       = $frame_ownerid;
            $parsedFrame['groupsymbol']   = ord(substr($parsedFrame['data'], $frame_offset++, 1));
            $parsedFrame['data']          = (string) substr($parsedFrame['data'], $frame_offset);

        } elseif (($id3v2_majorversion >= 3) && ($parsedFrame['frame_name'] == 'PRIV')) { // 4.27  PRIV Private frame (ID3v2.3+ only)
            //   The tag may contain more than one 'PRIV' frame
            //   but only with different contents
            // <Header for 'Private frame', ID: 'PRIV'>
            // Owner identifier      <text string> $00
            // The private data      <binary data>

            $frame_offset = 0;
            $frame_terminatorpos = strpos($parsedFrame['data'], "\x00", $frame_offset);
            $frame_ownerid = substr($parsedFrame['data'], $frame_offset, $frame_terminatorpos - $frame_offset);
            if (ord($frame_ownerid) === 0) {
                $frame_ownerid = '';
            }
            $frame_offset = $frame_terminatorpos + strlen("\x00");

            $parsedFrame['ownerid'] = $frame_ownerid;
            $parsedFrame['data']    = (string) substr($parsedFrame['data'], $frame_offset);

        } elseif (($id3v2_majorversion >= 4) && ($parsedFrame['frame_name'] == 'SIGN')) { // 4.28  SIGN Signature frame (ID3v2.4+ only)
            //   There may be more than one 'signature frame' in a tag,
            //   but no two may be identical
            // <Header for 'Signature frame', ID: 'SIGN'>
            // Group symbol      $xx
            // Signature         <binary data>

            $frame_offset = 0;
            $parsedFrame['groupsymbol'] = ord(substr($parsedFrame['data'], $frame_offset++, 1));
            $parsedFrame['data']        = (string) substr($parsedFrame['data'], $frame_offset);

        } elseif (($id3v2_majorversion >= 4) && ($parsedFrame['frame_name'] == 'SEEK')) { // 4.29  SEEK Seek frame (ID3v2.4+ only)
            //   There may only be one 'seek frame' in a tag
            // <Header for 'Seek frame', ID: 'SEEK'>
            // Minimum offset to next tag       $xx xx xx xx

            $frame_offset = 0;
            $parsedFrame['data']          = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], $frame_offset, 4));

        } elseif (($id3v2_majorversion >= 4) && ($parsedFrame['frame_name'] == 'ASPI')) { // 4.30  ASPI Audio seek point index (ID3v2.4+ only)
            //   There may only be one 'audio seek point index' frame in a tag
            // <Header for 'Seek Point Index', ID: 'ASPI'>
            // Indexed data start (S)         $xx xx xx xx
            // Indexed data length (L)        $xx xx xx xx
            // Number of index points (N)     $xx xx
            // Bits per index point (b)       $xx
            //   Then for every index point the following data is included:
            // Fraction at index (Fi)          $xx (xx)

            $frame_offset = 0;
            $parsedFrame['datastart'] = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], $frame_offset, 4));
            $frame_offset += 4;
            $parsedFrame['indexeddatalength'] = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], $frame_offset, 4));
            $frame_offset += 4;
            $parsedFrame['indexpoints'] = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], $frame_offset, 2));
            $frame_offset += 2;
            $parsedFrame['bitsperpoint'] = ord(substr($parsedFrame['data'], $frame_offset++, 1));
            $frame_bytesperpoint = ceil($parsedFrame['bitsperpoint'] / 8);
            for ($i = 0; $i < $parsedFrame['indexpoints']; $i++) {
                $parsedFrame['indexes'][$i] = getid3_lib::BigEndian2Int(substr($parsedFrame['data'], $frame_offset, $frame_bytesperpoint));
                $frame_offset += $frame_bytesperpoint;
            }
            unset($parsedFrame['data']);

        } elseif (($id3v2_majorversion >= 3) && ($parsedFrame['frame_name'] == 'RGAD')) { // Replay Gain Adjustment
            // http://privatewww.essex.ac.uk/~djmrob/replaygain/file_format_id3v2.html
            //   There may only be one 'RGAD' frame in a tag
            // <Header for 'Replay Gain Adjustment', ID: 'RGAD'>
            // Peak Amplitude                      $xx $xx $xx $xx
            // Radio Replay Gain Adjustment        %aaabbbcd %dddddddd
            // Audiophile Replay Gain Adjustment   %aaabbbcd %dddddddd
            //   a - name code
            //   b - originator code
            //   c - sign bit
            //   d - replay gain adjustment

            $frame_offset = 0;
            $parsedFrame['peakamplitude'] = getid3_lib::BigEndian2Float(substr($parsedFrame['data'], $frame_offset, 4));
            $frame_offset += 4;
            $rg_track_adjustment = getid3_lib::Dec2Bin(substr($parsedFrame['data'], $frame_offset, 2));
            $frame_offset += 2;
            $rg_album_adjustment = getid3_lib::Dec2Bin(substr($parsedFrame['data'], $frame_offset, 2));
            $frame_offset += 2;
            $parsedFrame['raw']['track']['name']       = getid3_lib::Bin2Dec(substr($rg_track_adjustment, 0, 3));
            $parsedFrame['raw']['track']['originator'] = getid3_lib::Bin2Dec(substr($rg_track_adjustment, 3, 3));
            $parsedFrame['raw']['track']['signbit']    = getid3_lib::Bin2Dec(substr($rg_track_adjustment, 6, 1));
            $parsedFrame['raw']['track']['adjustment'] = getid3_lib::Bin2Dec(substr($rg_track_adjustment, 7, 9));
            $parsedFrame['raw']['album']['name']       = getid3_lib::Bin2Dec(substr($rg_album_adjustment, 0, 3));
            $parsedFrame['raw']['album']['originator'] = getid3_lib::Bin2Dec(substr($rg_album_adjustment, 3, 3));
            $parsedFrame['raw']['album']['signbit']    = getid3_lib::Bin2Dec(substr($rg_album_adjustment, 6, 1));
            $parsedFrame['raw']['album']['adjustment'] = getid3_lib::Bin2Dec(substr($rg_album_adjustment, 7, 9));
            $parsedFrame['track']['name']       = getid3_lib::RGADnameLookup($parsedFrame['raw']['track']['name']);
            $parsedFrame['track']['originator'] = getid3_lib::RGADoriginatorLookup($parsedFrame['raw']['track']['originator']);
            $parsedFrame['track']['adjustment'] = getid3_lib::RGADadjustmentLookup($parsedFrame['raw']['track']['adjustment'], $parsedFrame['raw']['track']['signbit']);
            $parsedFrame['album']['name']       = getid3_lib::RGADnameLookup($parsedFrame['raw']['album']['name']);
            $parsedFrame['album']['originator'] = getid3_lib::RGADoriginatorLookup($parsedFrame['raw']['album']['originator']);
            $parsedFrame['album']['adjustment'] = getid3_lib::RGADadjustmentLookup($parsedFrame['raw']['album']['adjustment'], $parsedFrame['raw']['album']['signbit']);

            $info['replay_gain']['track']['peak']       = $parsedFrame['peakamplitude'];
            $info['replay_gain']['track']['originator'] = $parsedFrame['track']['originator'];
            $info['replay_gain']['track']['adjustment'] = $parsedFrame['track']['adjustment'];
            $info['replay_gain']['album']['originator'] = $parsedFrame['album']['originator'];
            $info['replay_gain']['album']['adjustment'] = $parsedFrame['album']['adjustment'];

            unset($parsedFrame['data']);

        }

        return true;
    }

    public function DeUnsynchronise($data) {
        return str_replace("\xFF\x00", "\xFF", $data);
    }

    public function LookupExtendedHeaderRestrictionsTagSizeLimits($index) {
        static $LookupExtendedHeaderRestrictionsTagSizeLimits = array(
            0x00 => 'No more than 128 frames and 1 MB total tag size',
            0x01 => 'No more than 64 frames and 128 KB total tag size',
            0x02 => 'No more than 32 frames and 40 KB total tag size',
            0x03 => 'No more than 32 frames and 4 KB total tag size',
        );

        return (isset($LookupExtendedHeaderRestrictionsTagSizeLimits[$index]) ? $LookupExtendedHeaderRestrictionsTagSizeLimits[$index] : '');
    }

    public function LookupExtendedHeaderRestrictionsTextEncodings($index) {
        static $LookupExtendedHeaderRestrictionsTextEncodings = array(
            0x00 => 'No restrictions',
            0x01 => 'Strings are only encoded with ISO-8859-1 or UTF-8',
        );

        return (isset($LookupExtendedHeaderRestrictionsTextEncodings[$index]) ? $LookupExtendedHeaderRestrictionsTextEncodings[$index] : '');
    }

    public function LookupExtendedHeaderRestrictionsTextFieldSize($index) {
        static $LookupExtendedHeaderRestrictionsTextFieldSize = array(
            0x00 => 'No restrictions',
            0x01 => 'No string is longer than 1024 characters',
            0x02 => 'No string is longer than 128 characters',
            0x03 => 'No string is longer than 30 characters',
        );

        return (isset($LookupExtendedHeaderRestrictionsTextFieldSize[$index]) ? $LookupExtendedHeaderRestrictionsTextFieldSize[$index] : '');
    }

    public function LookupExtendedHeaderRestrictionsImageEncoding($index) {
        static $LookupExtendedHeaderRestrictionsImageEncoding = array(
            0x00 => 'No restrictions',
            0x01 => 'Images are encoded only with PNG or JPEG',
        );

        return (isset($LookupExtendedHeaderRestrictionsImageEncoding[$index]) ? $LookupExtendedHeaderRestrictionsImageEncoding[$index] : '');
    }

    public function LookupExtendedHeaderRestrictionsImageSizeSize($index) {
        static $LookupExtendedHeaderRestrictionsImageSizeSize = array(
            0x00 => 'No restrictions',
            0x01 => 'All images are 256x256 pixels or smaller',
            0x02 => 'All images are 64x64 pixels or smaller',
            0x03 => 'All images are exactly 64x64 pixels, unless required otherwise',
        );

        return (isset($LookupExtendedHeaderRestrictionsImageSizeSize[$index]) ? $LookupExtendedHeaderRestrictionsImageSizeSize[$index] : '');
    }

    public function LookupCurrencyUnits($currencyid) {

        $begin = __LINE__;

        /** This is not a comment!
         
         */

        return getid3_lib::EmbeddedLookup($currencyid, $begin, __LINE__, __FILE__, 'id3v2-currency-units');
    }

    public function LookupCurrencyCountry($currencyid) {

        $begin = __LINE__;

        /** This is not a comment!
         
         */

        return getid3_lib::EmbeddedLookup($currencyid, $begin, __LINE__, __FILE__, 'id3v2-currency-country');
    }

    public static function LanguageLookup($languagecode, $casesensitive=false) {

        if (!$casesensitive) {
            $languagecode = strtolower($languagecode);
        }

        // http://www.id3.org/id3v2.4.0-structure.txt
        // [4.   ID3v2 frame overview]
        // The three byte language field, present in several frames, is used to
        // describe the language of the frame's content, according to ISO-639-2
        // [ISO-639-2]. The language should be represented in lower case. If the
        // language is not known the string "XXX" should be used.

        // ISO 639-2 - http://www.id3.org/iso639-2.html

        $begin = __LINE__;

        /** This is not a comment!
         
         */

        return getid3_lib::EmbeddedLookup($languagecode, $begin, __LINE__, __FILE__, 'id3v2-languagecode');
    }

    public static function ETCOEventLookup($index) {
        if (($index >= 0x17) && ($index <= 0xDF)) {
            return 'reserved for future use';
        }
        if (($index >= 0xE0) && ($index <= 0xEF)) {
            return 'not predefined synch 0-F';
        }
        if (($index >= 0xF0) && ($index <= 0xFC)) {
            return 'reserved for future use';
        }

        static $EventLookup = array(
            0x00 => 'padding (has no meaning)',
            0x01 => 'end of initial silence',
            0x02 => 'intro start',
            0x03 => 'main part start',
            0x04 => 'outro start',
            0x05 => 'outro end',
            0x06 => 'verse start',
            0x07 => 'refrain start',
            0x08 => 'interlude start',
            0x09 => 'theme start',
            0x0A => 'variation start',
            0x0B => 'key change',
            0x0C => 'time change',
            0x0D => 'momentary unwanted noise (Snap, Crackle & Pop)',
            0x0E => 'sustained noise',
            0x0F => 'sustained noise end',
            0x10 => 'intro end',
            0x11 => 'main part end',
            0x12 => 'verse end',
            0x13 => 'refrain end',
            0x14 => 'theme end',
            0x15 => 'profanity',
            0x16 => 'profanity end',
            0xFD => 'audio end (start of silence)',
            0xFE => 'audio file ends',
            0xFF => 'one more byte of events follows',
        );

        return (isset($EventLookup[$index]) ? $EventLookup[$index] : '');
    }

    public static function SYTLContentTypeLookup($index) {
        static $SYTLContentTypeLookup = array(
            0x00 => 'other',
            0x01 => 'lyrics',
            0x02 => 'text transcription',
            0x03 => 'movement/part name', // (e.g. 'Adagio')
            0x04 => 'events',             // (e.g. 'Don Quijote enters the stage')
            0x05 => 'chord',              // (e.g. 'Bb F Fsus')
            0x06 => 'trivia/\'pop up\' information',
            0x07 => 'URLs to webpages',
            0x08 => 'URLs to images',
        );

        return (isset($SYTLContentTypeLookup[$index]) ? $SYTLContentTypeLookup[$index] : '');
    }

    public static function APICPictureTypeLookup($index, $returnarray=false) {
        static $APICPictureTypeLookup = array(
            0x00 => 'Other',
            0x01 => '32x32 pixels \'file icon\' (PNG only)',
            0x02 => 'Other file icon',
            0x03 => 'Cover (front)',
            0x04 => 'Cover (back)',
            0x05 => 'Leaflet page',
            0x06 => 'Media (e.g. label side of CD)',
            0x07 => 'Lead artist/lead performer/soloist',
            0x08 => 'Artist/performer',
            0x09 => 'Conductor',
            0x0A => 'Band/Orchestra',
            0x0B => 'Composer',
            0x0C => 'Lyricist/text writer',
            0x0D => 'Recording Location',
            0x0E => 'During recording',
            0x0F => 'During performance',
            0x10 => 'Movie/video screen capture',
            0x11 => 'A bright coloured fish',
            0x12 => 'Illustration',
            0x13 => 'Band/artist logotype',
            0x14 => 'Publisher/Studio logotype',
        );
        if ($returnarray) {
            return $APICPictureTypeLookup;
        }

        return (isset($APICPictureTypeLookup[$index]) ? $APICPictureTypeLookup[$index] : '');
    }

    public static function COMRReceivedAsLookup($index) {
        static $COMRReceivedAsLookup = array(
            0x00 => 'Other',
            0x01 => 'Standard CD album with other songs',
            0x02 => 'Compressed audio on CD',
            0x03 => 'File over the Internet',
            0x04 => 'Stream over the Internet',
            0x05 => 'As note sheets',
            0x06 => 'As note sheets in a book with other sheets',
            0x07 => 'Music on other media',
            0x08 => 'Non-musical merchandise',
        );

        return (isset($COMRReceivedAsLookup[$index]) ? $COMRReceivedAsLookup[$index] : '');
    }

    public static function RVA2ChannelTypeLookup($index) {
        static $RVA2ChannelTypeLookup = array(
            0x00 => 'Other',
            0x01 => 'Master volume',
            0x02 => 'Front right',
            0x03 => 'Front left',
            0x04 => 'Back right',
            0x05 => 'Back left',
            0x06 => 'Front centre',
            0x07 => 'Back centre',
            0x08 => 'Subwoofer',
        );

        return (isset($RVA2ChannelTypeLookup[$index]) ? $RVA2ChannelTypeLookup[$index] : '');
    }

    public static function FrameNameLongLookup($framename) {

        $begin = __LINE__;

        /** This is not a comment!
         
         */

        return getid3_lib::EmbeddedLookup($framename, $begin, __LINE__, __FILE__, 'id3v2-framename_long');

        // Last three:
        // from Helium2 [www.helium2.com]
        // from http://privatewww.essex.ac.uk/~djmrob/replaygain/file_format_id3v2.html
    }

    public static function FrameNameShortLookup($framename) {

        $begin = __LINE__;

        /** This is not a comment!
         
         */

        return getid3_lib::EmbeddedLookup($framename, $begin, __LINE__, __FILE__, 'id3v2-framename_short');
    }

    public static function TextEncodingTerminatorLookup($encoding) {
        // http://www.id3.org/id3v2.4.0-structure.txt
        // Frames that allow different types of text encoding contains a text encoding description byte. Possible encodings:
        static $TextEncodingTerminatorLookup = array(
            0   => "\x00",     // $00  ISO-8859-1. Terminated with $00.
            1   => "\x00\x00", // $01  UTF-16 encoded Unicode with BOM. All strings in the same frame SHALL have the same byteorder. Terminated with $00 00.
            2   => "\x00\x00", // $02  UTF-16BE encoded Unicode without BOM. Terminated with $00 00.
            3   => "\x00",     // $03  UTF-8 encoded Unicode. Terminated with $00.
            255 => "\x00\x00",
        );

        return (isset($TextEncodingTerminatorLookup[$encoding]) ? $TextEncodingTerminatorLookup[$encoding] : '');
    }

    public static function TextEncodingNameLookup($encoding) {
        // http://www.id3.org/id3v2.4.0-structure.txt
        // Frames that allow different types of text encoding contains a text encoding description byte. Possible encodings:
        static $TextEncodingNameLookup = array(
            0   => 'ISO-8859-1', // $00  ISO-8859-1. Terminated with $00.
            1   => 'UTF-16',     // $01  UTF-16 encoded Unicode with BOM. All strings in the same frame SHALL have the same byteorder. Terminated with $00 00.
            2   => 'UTF-16BE',   // $02  UTF-16BE encoded Unicode without BOM. Terminated with $00 00.
            3   => 'UTF-8',      // $03  UTF-8 encoded Unicode. Terminated with $00.
            255 => 'UTF-16BE',
        );

        return (isset($TextEncodingNameLookup[$encoding]) ? $TextEncodingNameLookup[$encoding] : 'ISO-8859-1');
    }

    public static function IsValidID3v2FrameName($framename, $id3v2majorversion) {
        switch ($id3v2majorversion) {
            case 2:
                return preg_match('#[A-Z][A-Z0-9]{2}#', $framename);
                break;

            case 3:
            case 4:
                return preg_match('#[A-Z][A-Z0-9]{3}#', $framename);
                break;
        }

        return false;
    }

    public static function IsANumber($numberstring, $allowdecimal=false, $allownegative=false) {
        for ($i = 0; $i < strlen($numberstring); $i++) {
            if ((chr($numberstring{$i}) < chr('0')) || (chr($numberstring{$i}) > chr('9'))) {
                if (($numberstring{$i} == '.') && $allowdecimal) {
                    // allowed
                } elseif (($numberstring{$i} == '-') && $allownegative && ($i == 0)) {
                    // allowed
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    public static function IsValidDateStampString($datestamp) {
        if (strlen($datestamp) != 8) {
            return false;
        }
        if (!self::IsANumber($datestamp, false)) {
            return false;
        }
        $year  = substr($datestamp, 0, 4);
        $month = substr($datestamp, 4, 2);
        $day   = substr($datestamp, 6, 2);
        if (($year == 0) || ($month == 0) || ($day == 0)) {
            return false;
        }
        if ($month > 12) {
            return false;
        }
        if ($day > 31) {
            return false;
        }
        if (($day > 30) && (($month == 4) || ($month == 6) || ($month == 9) || ($month == 11))) {
            return false;
        }
        if (($day > 29) && ($month == 2)) {
            return false;
        }

        return true;
    }

    public static function ID3v2HeaderLength($majorversion) {
        return (($majorversion == 2) ? 6 : 10);
    }

}
