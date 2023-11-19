<?php
    function timeAgo($timestamp, $keyword) {
        $currentDate = new DateTime();
        $targetDate = new DateTime($timestamp);
    
        $timeDifference = $currentDate->getTimestamp() - $targetDate->getTimestamp();
    
        $seconds = floor($timeDifference);
        $minutes = floor($seconds / 60);
        $hours = floor($minutes / 60);
        $days = floor($hours / 24);
        $months = floor($days / 30.44);
        $years = floor($months / 12);
    
        $result = '';

        if ($years > 0) {
        $result = " $keyword $years year" . ($years > 1 ? 's' : '') . ' ago';
        } elseif ($months > 0) {
        $result = "$keyword $months month" . ($months > 1 ? 's' : '') . ' ago';
        } elseif ($days > 0) {
        $result = "$keyword $days day" . ($days > 1 ? 's' : '') . ' ago';
        } elseif ($hours > 0) {
        $result = "$keyword $hours hour" . ($hours > 1 ? 's' : '') . ' ago';
        } elseif ($minutes > 0) {
        $result = "$keyword $minutes minute" . ($minutes > 1 ? 's' : '') . ' ago';
        } else {
        $result = "$keyword $seconds second" . ($seconds > 1 ? 's' : '') . ' ago';
        }
    
        return "<p>".$result."</p>";
    }
?>