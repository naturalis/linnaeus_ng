// ActionScript file
private var menubarXML:XMLList =
<>
    <menuitem label="File" data="top">
        <menuitem label="MenuItem 1-A" data="1A"/>
        <menuitem label="MenuItem 1-B" data="1B"/>
    </menuitem>
    <menuitem label="Window" data="top">
        <menuitem label="MenuItem 2-A" type="check"  data="2A"/>
        <menuitem type="separator"/>
        <menuitem label="MenuItem 2-B" >
            <menuitem label="SubMenuItem 3-A" type="radio"
                groupName="one" data="3A"/>
            <menuitem label="SubMenuItem 3-B" type="radio"
                groupName="one" data="3B"/>
        </menuitem>
    </menuitem>
     <menuitem label="Settings" data="top">
     </menuitem>
    <menuitem label="Help" data="top">
     <menuitem label="Help Contents" data="5A"/>
     <menuitem label="About Linnaeus Next Generation" data="5b"/>
    </menuitem>
</>;
